<?php

namespace App\Http\Controllers;
use App\Models\Activity;
use App\Models\SchoolClass;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ClassTaskController extends Controller
{
    // =====================================================
    // 1. MEMERIKSA ROLE GURU
    // =====================================================

    private function isTeacher(Request $request): bool
    {
        return in_array(
            $request->user()->role,
            ['guru', 'admin'],
            true
        );
    }

    // =====================================================
    // 2. MEMERIKSA KEPEMILIKAN KELAS GURU
    // =====================================================

    private function ownedClass(
        Request $request,
        int $classId
    ): SchoolClass {
        abort_unless($this->isTeacher($request), 403);

        return $request->user()
            ->teachingClasses()
            ->findOrFail($classId);
    }

    // =====================================================
    // 3. MEMERIKSA KEANGGOTAAN SISWA DALAM KELAS
    // =====================================================

    private function isEnrolled(
        Request $request,
        int $classId
    ): bool {
        return $request->user()
            ->classes()
            ->whereKey($classId)
            ->exists();
    }

    // =====================================================
    // 4. MENAMPILKAN DAFTAR TUGAS GURU DAN SISWA
    // =====================================================

    public function index(Request $request)
    {
        $teacher = $this->isTeacher($request);

        $classes = $teacher
            ? $request->user()->teachingClasses()->get()
            : $request->user()->classes()->get();

        $tasks = Activity::query()
            ->where('mode', 'tugas')
            ->whereIn('class_id', $classes->pluck('id'))
            ->with(
                $teacher
                    ? ['submissions.user']
                    : [
                        'submissions' => function ($query) use ($request) {
                            $query->where(
                                'user_id',
                                $request->user()->id
                            );
                        }
                    ]
            )
            ->latest()
            ->get();

        return view('pages.guru.class-tasks', compact(
            'teacher',
            'classes',
            'tasks'
        ));
    }

    // =====================================================
    // 5. GURU MEMBUAT TUGAS BARU
    // =====================================================

    public function store(Request $request)
    {
        abort_unless($this->isTeacher($request), 403);

        $data = $request->validate([
            'class_id' => 'required|integer|exists:classes,id',
            'title' => 'required|string|max:255',
            'instructions' => 'required|string|max:10000',
            'fpbl_stage' => 'required|integer|between:1,4',
            'due_at' => 'nullable|date|after:now',
        ]);

        $class = $this->ownedClass(
            $request,
            (int) $data['class_id']
        );

        Activity::create([
            'class_id' => $class->id,
            'title' => $data['title'],
            'instructions' => $data['instructions'],
            'fpbl_stage' => $data['fpbl_stage'],
            'mode' => 'tugas',
            'starts_at' => now(),
            'due_at' => $data['due_at'] ?? null,
        ]);

        return redirect()
            ->route('classroom.tasks.index')
            ->with(
                'success',
                'Tugas berhasil dibuat!'
            );
    }

    // =====================================================
    // 6. SISWA MENGUMPULKAN TUGAS
    // =====================================================

    public function submit(
        Request $request,
        Activity $activity
    ) {
        // Hanya siswa yang boleh mengumpulkan tugas
        abort_unless(
            $request->user()->role === 'siswa',
            403
        );

        // Memastikan siswa mengikuti kelas terkait
        abort_unless(
            $activity->mode === 'tugas'
            && $activity->class_id
            && $this->isEnrolled(
                $request,
                (int) $activity->class_id
            ),
            403
        );

        // Memeriksa batas waktu pengumpulan
        if (
            $activity->due_at
            && now()->greaterThan($activity->due_at)
        ) {
            return back()->withErrors([
                'tugas' => 'Batas pengumpulan sudah lewat.'
            ]);
        }

        // Validasi jawaban dan file
        $data = $request->validate([
            'content' => 'nullable|string|max:5000',

            'file' => [
                'nullable',
                'file',
                'mimes:pdf,doc,docx,jpg,jpeg,png,zip',
                'max:10240',
            ],
        ]);

        // Siswa wajib mengisi jawaban atau mengunggah file
        if (
            blank($data['content'] ?? null)
            && !$request->hasFile('file')
        ) {
            throw ValidationException::withMessages([
                'content' => 'Isi jawaban atau upload file.'
            ]);
        }

        // Mencari pengumpulan sebelumnya
        $submission = Submission::firstOrNew([
            'activity_id' => $activity->id,
            'user_id' => $request->user()->id,
        ]);

        // Tugas yang sudah dinilai tidak bisa diubah
        if (
            $submission->exists
            && $submission->score !== null
        ) {
            return back()->withErrors([
                'tugas' => 'Tugas sudah dinilai.'
            ]);
        }

        // Menyimpan lokasi file sebelumnya
        $oldFile = $submission->file_path;

        // Menyimpan file baru jika diunggah
        if ($request->hasFile('file')) {

            $submission->file_path = $request
                ->file('file')
                ->store(
                    'mathleaf/tasks',
                    'local'
                );
        }

        // Menyimpan isi jawaban
        $submission->content =
            $data['content'] ?? $submission->content;

        $submission->study_group_id = null;
        $submission->submitted_at = now();

        $submission->save();

        // Menghapus file sebelumnya jika diganti
        if (
            $request->hasFile('file')
            && $oldFile
            && $oldFile !== $submission->file_path
        ) {
            Storage::disk('local')->delete($oldFile);
        }

        return redirect()
            ->route('classroom.tasks.index')
            ->with(
                'success',
                'Tugas berhasil dikumpulkan!'
            );
    }

    // =====================================================
    // 7. GURU MEMBERIKAN NILAI KEPADA SISWA
    // =====================================================

    public function grade(
        Request $request,
        Submission $submission
    ) {
        $activity = $submission->activity;

        // Memastikan tugas tersedia
        abort_unless(
            $activity
            && $activity->mode === 'tugas'
            && $activity->class_id,
            404
        );

        // Hanya guru pemilik kelas yang dapat menilai
        $this->ownedClass(
            $request,
            (int) $activity->class_id
        );

        // Validasi nilai dan umpan balik
        $data = $request->validate([
            'score' => 'required|numeric|between:0,100',
            'teacher_feedback' => 'nullable|string|max:3000',
        ]);

        // Menyimpan hasil penilaian
        $submission->update([
            'score' => $data['score'],

            'teacher_feedback' =>
                $data['teacher_feedback'] ?? null,

            'graded_by' => $request->user()->id,
        ]);

        return redirect()
            ->route('classroom.tasks.index')
            ->with(
                'success',
                'Nilai berhasil disimpan!'
            );
    }

    // =====================================================
    // 8. DOWNLOAD FILE TUGAS SISWA
    // =====================================================

    public function download(
        Request $request,
        Submission $submission
    ) {
        $activity = $submission->activity;

        // Memastikan file berasal dari aktivitas tugas
        abort_unless(
            $activity
            && $activity->mode === 'tugas'
            && $activity->class_id,
            404
        );

        // Memeriksa hak akses
        if ($this->isTeacher($request)) {

            $allowed = $request->user()
                ->teachingClasses()
                ->whereKey($activity->class_id)
                ->exists();

        } else {

            $allowed =
                $request->user()->role === 'siswa'
                && $submission->user_id === $request->user()->id
                && $this->isEnrolled(
                    $request,
                    (int) $activity->class_id
                );
        }

        abort_unless($allowed, 403);

        // Menentukan penyimpanan lokal
        // Anotasi ini membantu Intelephense
        // mengenali method download() dan exists()

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('local');

        // Memastikan file tersedia
        abort_unless(
            $submission->file_path
            && $disk->exists($submission->file_path),
            404
        );

        // Mengunduh file tugas
        return $disk->download(
            $submission->file_path
        );
    }
}
