<?php

namespace App\Http\Controllers;

use App\Models\AssessmentAttempt;
use App\Models\Submission;
use Illuminate\Http\Request;
class TeacherGradesController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Hanya guru dan admin
        abort_unless(
            in_array($user->role, ['guru', 'admin'], true),
            403
        );

        // Ambil kelas yang diajar guru
        $classes = $user->teachingClasses()
            ->orderBy('name')
            ->get(['id', 'name']);

        $request->validate([
            'class_id' => ['nullable', 'integer'],
        ]);

        $selectedClassId = $request->filled('class_id')
            ? (int) $request->query('class_id')
            : null;

        // Guru tidak boleh melihat kelas orang lain
        if ($selectedClassId !== null) {
            abort_unless(
                $classes->contains('id', $selectedClassId),
                403
            );
        }

        $classIds = $selectedClassId !== null
            ? [$selectedClassId]
            : $classes->pluck('id')->all();

        // Pengumpulan tugas
        $taskQuery = Submission::query()->whereHas(
            'activity',
            fn ($query) => $query
                ->where('mode', 'tugas')
                ->whereIn('class_id', $classIds)
        );

        // Hasil pengerjaan kuis
        $quizQuery = AssessmentAttempt::query()->whereHas(
            'assessment',
            fn ($query) => $query
                ->whereIn('class_id', $classIds)
        );

        // Statistik tugas
        $taskCount = (clone $taskQuery)->count();

        $taskGradedCount = (clone $taskQuery)
            ->whereNotNull('score')
            ->count();

        $taskAverage = (clone $taskQuery)
            ->whereNotNull('score')
            ->avg('score');

        // Statistik kuis
        $quizCount = (clone $quizQuery)->count();

        $quizGradedCount = (clone $quizQuery)
            ->whereNotNull('score')
            ->count();

        $quizAverage = (clone $quizQuery)
            ->whereNotNull('score')
            ->avg('score');

        // Data tugas dan nama siswa
        $taskSubmissions = (clone $taskQuery)
            ->with([
                'user:id,name',
                'activity:id,title,class_id',
            ])
            ->latest('submitted_at')
            ->paginate(10, ['*'], 'tasks_page')
            ->withQueryString();

        // Data kuis dan nama siswa
        $quizAttempts = (clone $quizQuery)
            ->with([
                'user:id,name',
                'assessment:id,title,class_id',
            ])
            ->latest('submitted_at')
            ->paginate(10, ['*'], 'quizzes_page')
            ->withQueryString();

                // AUTO_FIX_LIST_SISWA
        // Ambil siswa dari kelas yang diajar guru
        $students = \App\Models\User::query()
            ->whereHas('classes', function ($query) use ($classIds) {
                $query->whereIn('classes.id', $classIds);
            })
            ->orderBy('name')
            ->get(['id', 'name']);

return view('pages.teacher-grades', compact(
            'students',
            'classes',
            'selectedClassId',
            'taskCount',
            'taskGradedCount',
            'taskAverage',
            'quizCount',
            'quizGradedCount',
            'quizAverage',
            'taskSubmissions',
            'quizAttempts'


        ));


        // Ambil daftar siswa dari kelas yang diajar guru
        $students = \App\Models\User::query()
            ->whereHas('classes', function ($query) use ($classIds) {
                $query->whereIn('classes.id', $classIds);
            })
            ->orderBy('name')
            ->get(['id', 'name']);

    }


    // =====================================================
    // EXPORT REKAP NILAI GURU KE CSV / EXCEL
    // =====================================================

    public function export(Request $request)
    {
        $user = $request->user();

        // Hanya guru atau admin yang boleh export
        abort_unless(
            in_array($user->role, ['guru', 'admin'], true),
            403
        );

        $request->validate([
            'class_id' => ['nullable', 'integer'],
        ]);

        // Ambil kelas milik guru yang login
        $classes = $user->teachingClasses()
            ->get(['id', 'name']);

        $selectedClassId = $request->filled('class_id')
            ? (int) $request->query('class_id')
            : null;

        // Cegah akses ke nilai kelas guru lain
        if ($selectedClassId !== null) {
            abort_unless(
                $classes->contains('id', $selectedClassId),
                403
            );
        }

        $classIds = $selectedClassId !== null
            ? [$selectedClassId]
            : $classes->pluck('id')->all();

        $classNames = $classes->pluck('name', 'id');

        // Cegah teks pengguna dibaca sebagai formula Excel
        $safeText = static function ($value): string {
            $text = (string) ($value ?? '');

            return preg_match('/^\s*[=+\-@]/u', $text)
                ? "'" . $text
                : $text;
        };

        $filename = 'rekap-nilai-guru-'
            . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(
            function () use ($classIds, $classNames, $safeText) {

                $output = fopen('php://output', 'w');

                if ($output === false) {
                    return;
                }

                // UTF-8 agar karakter Indonesia terbaca
                fwrite($output, "\xEF\xBB\xBF");

                // Header CSV
                fputcsv($output, [
                    'Jenis',
                    'Nama Siswa',
                    'Kelas',
                    'Judul',
                    'Nilai',
                    'Status',
                    'Tanggal Pengumpulan',
                ], ';', '"', '');

                // ================================
                // DATA NILAI TUGAS
                // ================================

                Submission::query()
                    ->whereHas(
                        'activity',
                        function ($query) use ($classIds) {
                            $query->where('mode', 'tugas')
                                ->whereIn('class_id', $classIds);
                        }
                    )
                    ->with([
                        'user:id,name',
                        'activity:id,title,class_id',
                    ])
                    ->chunkById(
                        200,
                        function ($submissions) use (
                            $output,
                            $classNames,
                            $safeText
                        ) {
                            foreach ($submissions as $submission) {

                                fputcsv($output, [
                                    'Tugas',

                                    $safeText(
                                        $submission->user?->name
                                        ?? 'Akun tidak tersedia'
                                    ),

                                    $safeText(
                                        $classNames->get(
                                            $submission->activity?->class_id,
                                            'Kelas tidak tersedia'
                                        )
                                    ),

                                    $safeText(
                                        $submission->activity?->title
                                        ?? 'Tugas tidak tersedia'
                                    ),

                                    $submission->score === null
                                        ? ''
                                        : number_format(
                                            (float) $submission->score,
                                            2,
                                            ',',
                                            ''
                                        ),

                                    $submission->score === null
                                        ? 'Menunggu Nilai'
                                        : 'Sudah Dinilai',

                                    $submission->submitted_at?->format(
                                        'd/m/Y H:i'
                                    ) ?? '',

                                ], ';', '"', '');

                            }
                        }
                    );

                // ================================
                // DATA NILAI KUIS
                // ================================

                AssessmentAttempt::query()
                    ->whereHas(
                        'assessment',
                        function ($query) use ($classIds) {
                            $query->whereIn(
                                'class_id',
                                $classIds
                            );
                        }
                    )
                    ->with([
                        'user:id,name',
                        'assessment:id,title,class_id',
                    ])
                    ->chunkById(
                        200,
                        function ($attempts) use (
                            $output,
                            $classNames,
                            $safeText
                        ) {
                            foreach ($attempts as $attempt) {

                                fputcsv($output, [
                                    'Kuis',

                                    $safeText(
                                        $attempt->user?->name
                                        ?? 'Akun tidak tersedia'
                                    ),

                                    $safeText(
                                        $classNames->get(
                                            $attempt->assessment?->class_id,
                                            'Kelas tidak tersedia'
                                        )
                                    ),

                                    $safeText(
                                        $attempt->assessment?->title
                                        ?? 'Kuis tidak tersedia'
                                    ),

                                    $attempt->score === null
                                        ? ''
                                        : number_format(
                                            (float) $attempt->score,
                                            2,
                                            ',',
                                            ''
                                        ),

                                    $attempt->score === null
                                        ? 'Menunggu Nilai'
                                        : 'Sudah Dinilai',

                                    $attempt->submitted_at?->format(
                                        'd/m/Y H:i'
                                    ) ?? '',

                                ], ';', '"', '');

                            }
                        }
                    );

                fclose($output);
            },
            $filename,
            [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]
        );
    }



    // =====================================================
    // DETAIL NILAI PER SISWA
    // =====================================================

    public function student(
        Request $request,
        \App\Models\User $student
    ) {
        $teacher = $request->user();

        abort_unless(
            in_array($teacher->role, ['guru', 'admin'], true),
            403
        );

        $request->validate([
            'class_id' => ['nullable', 'integer'],
        ]);

        // Hanya kelas yang diajar guru dan diikuti siswa
        $classes = $teacher->teachingClasses()
            ->whereHas('students', function ($query) use ($student) {
                $query->whereKey($student->id);
            })
            ->orderBy('name')
            ->get(['id', 'name']);

        // Guru tidak boleh mengakses siswa kelas lain
        abort_if($classes->isEmpty(), 403);

        $selectedClassId = $request->filled('class_id')
            ? (int) $request->query('class_id')
            : null;

        if ($selectedClassId !== null) {
            abort_unless(
                $classes->contains('id', $selectedClassId),
                403
            );
        }

        $classIds = $selectedClassId !== null
            ? [$selectedClassId]
            : $classes->pluck('id')->all();

        // ==============================
        // DATA TUGAS SISWA
        // ==============================

        $taskQuery = Submission::query()
            ->where('user_id', $student->id)
            ->whereHas('activity', function ($query) use ($classIds) {
                $query->where('mode', 'tugas')
                    ->whereIn('class_id', $classIds);
            });

        $taskCount = (clone $taskQuery)->count();

        $taskGradedCount = (clone $taskQuery)
            ->whereNotNull('score')
            ->count();

        $taskAverage = (clone $taskQuery)
            ->whereNotNull('score')
            ->avg('score');

        $taskSubmissions = (clone $taskQuery)
            ->with('activity:id,title,class_id')
            ->orderByDesc('submitted_at')
            ->paginate(10, ['*'], 'tasks_page')
            ->withQueryString();

        // ==============================
        // DATA KUIS SISWA
        // ==============================

        $quizQuery = AssessmentAttempt::query()
            ->where('user_id', $student->id)
            ->whereHas('assessment', function ($query) use ($classIds) {
                $query->whereIn('class_id', $classIds);
            });

        $quizCount = (clone $quizQuery)->count();

        $quizGradedCount = (clone $quizQuery)
            ->whereNotNull('score')
            ->count();

        $quizAverage = (clone $quizQuery)
            ->whereNotNull('score')
            ->avg('score');

        $quizAttempts = (clone $quizQuery)
            ->with('assessment:id,title,class_id')
            ->orderByDesc('submitted_at')
            ->paginate(10, ['*'], 'quizzes_page')
            ->withQueryString();

        return view('pages.teacher-student-grades', compact(
            'student',
            'classes',
            'selectedClassId',
            'taskCount',
            'taskGradedCount',
            'taskAverage',
            'taskSubmissions',
            'quizCount',
            'quizGradedCount',
            'quizAverage',
            'quizAttempts'
        ));
    }

}

