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
    private function isTeacher(Request $request): bool
    {
        return in_array($request->user()->role, ['guru', 'admin'], true);
    }

    private function ownedClass(Request $request, int $classId): SchoolClass
    {
        abort_unless($this->isTeacher($request), 403);
        return $request->user()->teachingClasses()->findOrFail($classId);
    }

    private function isEnrolled(Request $request, int $classId): bool
    {
        return $request->user()->classes()->whereKey($classId)->exists();
    }

    private function ownedTask(Request $request, Activity $activity): Activity
    {
        abort_unless($activity->mode === 'tugas' && $activity->class_id, 404);
        $this->ownedClass($request, (int) $activity->class_id);
        return $activity;
    }

    public function index(Request $request)
    {
        $teacher = $this->isTeacher($request);
        abort_unless($teacher || $request->user()->role === 'siswa', 403);

        $classes = ($teacher
            ? $request->user()->teachingClasses()
            : $request->user()->classes())->orderBy('name')->get();

        $data = $request->validate([
            'class_id' => ['nullable', 'integer'],
            'status' => ['nullable', 'in:semua,belum,dikumpulkan,dinilai'],
            'submission' => ['nullable', 'integer'],
        ]);
        $selectedClassId = isset($data['class_id']) ? (int) $data['class_id'] : null;
        if ($selectedClassId !== null) {
            abort_unless($classes->contains('id', $selectedClassId), 403);
        }
        $selectedStatus = $data['status'] ?? 'semua';
        $classIds = $selectedClassId !== null ? [$selectedClassId] : $classes->pluck('id')->all();
        $selectedTaskId = null;
        $selectedSubmissionId = null;
        if ($teacher && isset($data['submission'])) {
            $submission = Submission::query()
                ->whereKey((int) $data['submission'])
                ->whereHas('activity', fn ($query) => $query
                    ->where('mode', 'tugas')
                    ->whereIn('class_id', $classIds))
                ->firstOrFail();
            $selectedTaskId = (int) $submission->activity_id;
            $selectedSubmissionId = (int) $submission->id;
        }

        $tasks = Activity::query()
            ->where('mode', 'tugas')
            ->whereIn('class_id', $classIds)
            ->with($teacher
                ? ['submissions.user:id,name']
                : ['submissions' => fn ($q) => $q->where('user_id', $request->user()->id)])
            ->latest()
            ->get();

        if ($teacher) {
            return view('pages.guru.class-tasks', compact(
                'teacher', 'classes', 'tasks', 'selectedClassId',
                'selectedTaskId', 'selectedSubmissionId'
            ));
        }

        $allTasks = $tasks;
        $submittedCount = $allTasks->filter(fn ($task) => $task->submissions->isNotEmpty())->count();
        $gradedCount = $allTasks->filter(fn ($task) => $task->submissions->first()?->score !== null)->count();
        $pendingCount = $allTasks->count() - $submittedCount;
        $tasks = $allTasks->filter(function ($task) use ($selectedStatus) {
            $submission = $task->submissions->first();
            return match ($selectedStatus) {
                'belum' => $submission === null,
                'dikumpulkan' => $submission !== null,
                'dinilai' => $submission !== null && $submission->score !== null,
                default => true,
            };
        });

        return view('pages.siswa.class-tasks', compact(
            'classes', 'tasks', 'selectedClassId', 'selectedStatus',
            'submittedCount', 'gradedCount', 'pendingCount', 'allTasks'
        ));
    }

    public function store(Request $request)
    {
        abort_unless($this->isTeacher($request), 403);
        $data = $request->validate([
            'class_id' => ['required', 'integer', 'exists:classes,id'],
            'title' => ['required', 'string', 'max:255'],
            'instructions' => ['required', 'string', 'max:10000'],
            'fpbl_stage' => ['required', 'integer', 'between:1,4'],
            'due_at' => ['nullable', 'date', 'after:now'],
        ]);
        $class = $this->ownedClass($request, (int) $data['class_id']);
        Activity::create([
            'class_id' => $class->id,
            'title' => $data['title'],
            'instructions' => $data['instructions'],
            'fpbl_stage' => $data['fpbl_stage'],
            'mode' => 'tugas',
            'starts_at' => now(),
            'due_at' => $data['due_at'] ?? null,
        ]);
        return redirect()->route('classroom.tasks.index')->with('success', 'Tugas berhasil diterbitkan.');
    }

    public function edit(Request $request, Activity $activity)
    {
        $this->ownedTask($request, $activity);
        $classes = $request->user()->teachingClasses()->orderBy('name')->get();
        return view('pages.guru.edit-task', compact('activity', 'classes'));
    }

    public function update(Request $request, Activity $activity)
    {
        $this->ownedTask($request, $activity);
        $data = $request->validate([
            'class_id' => ['required', 'integer', 'exists:classes,id'],
            'title' => ['required', 'string', 'max:255'],
            'instructions' => ['required', 'string', 'max:10000'],
            'fpbl_stage' => ['required', 'integer', 'between:1,4'],
            'due_at' => ['nullable', 'date'],
        ]);
        $this->ownedClass($request, (int) $data['class_id']);
        if ((int) $data['class_id'] !== (int) $activity->class_id && $activity->submissions()->exists()) {
            throw ValidationException::withMessages([
                'class_id' => 'Kelas tidak dapat diganti karena sudah ada siswa yang mengumpulkan tugas.',
            ]);
        }
        $activity->update([
            'class_id' => (int) $data['class_id'],
            'title' => $data['title'],
            'instructions' => $data['instructions'],
            'fpbl_stage' => (int) $data['fpbl_stage'],
            'due_at' => $data['due_at'] ?? null,
        ]);
        return redirect()->route('classroom.tasks.index')->with('success', 'Tugas berhasil diperbarui.');
    }

    public function destroy(Request $request, Activity $activity)
    {
        $this->ownedTask($request, $activity);
        if ($activity->submissions()->exists()) {
            return back()->withErrors(['tugas' => 'Tugas tidak bisa dihapus karena sudah ada pengumpulan siswa.']);
        }
        $activity->delete();
        return redirect()->route('classroom.tasks.index')->with('success', 'Tugas yang belum memiliki pengumpulan berhasil dihapus.');
    }

    public function submit(Request $request, Activity $activity)
    {
        abort_unless($request->user()->role === 'siswa', 403);
        abort_unless($activity->mode === 'tugas' && $activity->class_id &&
            $this->isEnrolled($request, (int) $activity->class_id), 403);

        if ($activity->due_at && now()->greaterThan($activity->due_at)) {
            return back()->withErrors(['tugas' => 'Batas waktu pengumpulan sudah lewat.']);
        }
        $data = $request->validate([
            'content' => ['nullable', 'string', 'max:5000'],
            'file' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png,zip', 'max:10240'],
        ]);
        $submission = Submission::firstOrNew([
            'activity_id' => $activity->id,
            'user_id' => $request->user()->id,
        ]);
        if ($submission->exists && $submission->score !== null) {
            return back()->withErrors(['tugas' => 'Tugas sudah dinilai dan tidak dapat diubah.']);
        }
        if (blank($data['content'] ?? null) && !$request->hasFile('file') && !$submission->file_path) {
            throw ValidationException::withMessages(['content' => 'Isi jawaban atau unggah file terlebih dahulu.']);
        }

        $oldFile = $submission->file_path;
        if ($request->hasFile('file')) {
            $uploadedFile = $request->file('file');
            $submission->file_path = $uploadedFile->store('mathleaf/tasks', 'local');
            $submission->original_file_name = \Illuminate\Support\Str::limit($uploadedFile->getClientOriginalName(), 240, '');
            $submission->file_size_bytes = $uploadedFile->getSize();
        }
        $submission->content = $data['content'] ?? $submission->content;
        $submission->study_group_id = null;
        $submission->submitted_at = now();
        $submission->save();
        if ($request->hasFile('file') && $oldFile && $oldFile !== $submission->file_path) {
            Storage::disk('local')->delete($oldFile);
        }
        return redirect()->route('classroom.tasks.index')->with('success', 'Jawaban tugas berhasil disimpan.');
    }

    public function grade(Request $request, Submission $submission)
    {
        $activity = $submission->activity;
        abort_unless($activity && $activity->mode === 'tugas' && $activity->class_id, 404);
        $this->ownedClass($request, (int) $activity->class_id);
        $data = $request->validate([
            'score' => ['required', 'numeric', 'between:0,100'],
            'teacher_feedback' => ['nullable', 'string', 'max:3000'],
        ]);
        $submission->update([
            'score' => $data['score'],
            'teacher_feedback' => $data['teacher_feedback'] ?? null,
            'graded_by' => $request->user()->id,
        ]);
        return redirect()->route('classroom.tasks.index')->with('success', 'Nilai berhasil disimpan.');
    }

    public function download(Request $request, Submission $submission)
    {
        $activity = $submission->activity;
        abort_unless($activity && $activity->mode === 'tugas' && $activity->class_id, 404);
        $allowed = $this->isTeacher($request)
            ? $request->user()->teachingClasses()->whereKey($activity->class_id)->exists()
            : ($request->user()->role === 'siswa'
                && $submission->user_id === $request->user()->id
                && $this->isEnrolled($request, (int) $activity->class_id));
        abort_unless($allowed, 403);
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('local');
        abort_unless($submission->file_path && $disk->exists($submission->file_path), 404);

        // Pratinjau hanya untuk format aman yang didukung browser; izin akses tetap diperiksa di atas.
        $extension = strtolower(pathinfo($submission->file_path, PATHINFO_EXTENSION));
        if ($request->boolean('preview') && in_array($extension, ['pdf', 'jpg', 'jpeg', 'png'], true)) {
            $mime = match ($extension) {
                'pdf' => 'application/pdf',
                'png' => 'image/png',
                default => 'image/jpeg',
            };
            return response()->file($disk->path($submission->file_path), [
                'Content-Type' => $mime,
                'X-Content-Type-Options' => 'nosniff',
                'Content-Security-Policy' => "default-src 'none'; sandbox",
            ]);
        }
        return $disk->download($submission->file_path, $submission->original_file_name ?: null);
    }
}
