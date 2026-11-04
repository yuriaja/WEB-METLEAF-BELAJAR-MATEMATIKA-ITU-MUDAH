<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Support\QuizSchedule;

class StudentQuizController extends Controller
{
    private function student(Request $request): void
    {
        abort_unless($request->user()?->role === 'siswa', 403);
    }

    private function availableAssessment(Request $request, Assessment $assessment): Assessment
    {
        $this->student($request);
        QuizSchedule::archiveExpired();
        $assessment->refresh();
        QuizSchedule::assertOpen($assessment);

        $allowed = $assessment->is_published
            && $assessment->archived_at === null
            && $assessment->class_id !== null
            && $request->user()->classes()->whereKey($assessment->class_id)->exists();

        abort_unless($allowed, 403);

        return $assessment;
    }

    public function index(Request $request)
    {
        $this->student($request);
        QuizSchedule::archiveExpired();
        $user = $request->user();
        $classIds = $user->classes()->pluck('classes.id');

        $assessments = Assessment::query()
            ->whereIn('class_id', $classIds)
            ->where('is_published', true)
            ->whereNull('archived_at')
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('due_at')->orWhere('due_at', '>', now()))
            ->whereHas('questions')
            ->withCount('questions')
            ->latest()
            ->get();

        $attempts = AssessmentAttempt::query()
            ->where('user_id', $user->id)
            ->whereHas('assessment', fn ($query) => $query->whereIn('class_id', $classIds))
            ->with('assessment:id,title,type,class_id,passing_score,max_attempts')
            ->orderByDesc('submitted_at')
            ->get();

        $attemptCounts = $attempts->countBy('assessment_id');
        $completedIds = $attempts->filter(fn ($attempt) =>
            $attempt->score !== null && (float) $attempt->score >= (int) ($attempt->assessment?->passing_score ?? 70)
        )->pluck('assessment_id')->unique();
        $available = $assessments->filter(fn ($assessment) =>
            ($attemptCounts->get($assessment->id, 0)) < max(1, (int) $assessment->max_attempts)
            && !$completedIds->contains($assessment->id)
        );

        return view('pages.siswa.kuis', compact('available', 'attempts'));
    }

    public function take(Request $request, Assessment $assessment)
    {
        $this->availableAssessment($request, $assessment);
        $assessment->load('questions');

        abort_if($assessment->questions->isEmpty(), 404, 'Soal kuis belum tersedia.');

        $used = AssessmentAttempt::query()
            ->where('assessment_id', $assessment->id)
            ->where('user_id', $request->user()->id)
            ->count();

        $passed = AssessmentAttempt::query()->where('assessment_id', $assessment->id)
            ->where('user_id', $request->user()->id)
            ->whereNotNull('score')->where('score', '>=', (int) ($assessment->passing_score ?? 70))->exists();

        if ($passed) {
            return redirect()->route('siswa.kuis')->with('success', 'Kuis sudah tuntas. Tidak perlu remedial.');
        }
        if ($used >= max(1, (int) $assessment->max_attempts)) {
            return redirect()->route('siswa.kuis')
                ->with('error', 'Kesempatan mengerjakan kuis ini sudah habis.');
        }

        return view('pages.siswa.quiz-take', compact('assessment', 'used'));
    }

    public function submit(Request $request, Assessment $assessment)
    {
        $this->availableAssessment($request, $assessment);

        $request->validate([
            'answers' => ['required', 'array'],
            'answers.*' => ['required', 'integer', 'between:0,3'],
        ]);

        // Kunci baris assessment untuk menghindari dua pengiriman serentak
        // menghasilkan jumlah percobaan di atas batas yang ditentukan.
        $attempt = DB::transaction(function () use ($request, $assessment) {
            $assessment = Assessment::query()->lockForUpdate()->findOrFail($assessment->id);
            QuizSchedule::assertOpen($assessment);

            $used = AssessmentAttempt::query()
                ->where('assessment_id', $assessment->id)
                ->where('user_id', $request->user()->id)
                ->count();

            abort_if($used >= max(1, (int) $assessment->max_attempts), 403, 'Kesempatan kuis sudah habis.');
            $passed = AssessmentAttempt::query()
                ->where('assessment_id', $assessment->id)
                ->where('user_id', $request->user()->id)
                ->whereNotNull('score')->where('score', '>=', (int) ($assessment->passing_score ?? 70))->exists();
            abort_if($passed, 403, 'Kuis sudah tuntas. Tidak perlu remedial.');

            $questions = $assessment->questions()->get();
            abort_if($questions->isEmpty(), 404, 'Soal kuis belum tersedia.');

            $answers = $request->input('answers', []);
            $expectedIds = $questions->pluck('id')->map(fn ($id) => (string) $id)->sort()->values()->all();
            $givenIds = collect(array_keys($answers))->map(fn ($id) => (string) $id)->sort()->values()->all();
            if ($expectedIds !== $givenIds) {
                throw ValidationException::withMessages([
                    'answers' => 'Jawab semua soal kuis sebelum mengirim.',
                ]);
            }

            $attempt = AssessmentAttempt::create([
                'assessment_id' => $assessment->id,
                'user_id' => $request->user()->id,
                'attempt_number' => $used + 1,
                'status' => 'graded',
                'score' => 0,
                'submitted_at' => now(),
            ]);

            $earned = 0;
            $possible = $questions->sum('points');
            foreach ($questions as $question) {
                $answer = (string) $answers[$question->id];
                $correct = hash_equals((string) $question->correct_answer, $answer);
                $points = $correct ? (int) $question->points : 0;
                $earned += $points;
                $attempt->answers()->create([
                    'question_id' => $question->id,
                    'answer_text' => $answer,
                    'is_correct' => $correct,
                    'points_awarded' => $points,
                    'feedback' => $question->explanation,
                ]);
            }

            $attempt->update(['score' => $possible > 0 ? round($earned / $possible * 100, 2) : 0]);
            return $attempt;
        }, 3);

        return redirect()->route('siswa.quiz.result', $attempt)
            ->with('success', 'Jawabanmu berhasil disimpan.');
    }

    public function result(Request $request, AssessmentAttempt $attempt)
    {
        $this->student($request);
        abort_unless($attempt->user_id === $request->user()->id, 403);

        $attempt->load(['assessment.material', 'answers.question.material']);
        abort_unless(
            $attempt->assessment?->class_id !== null
                && $request->user()->classes()->whereKey($attempt->assessment->class_id)->exists(),
            403
        );

        $classId = (int) $attempt->assessment->class_id;
        $remedialMaterials = $attempt->answers->filter(fn ($answer) => !$answer->is_correct)
            ->map(fn ($answer) => $answer->question?->material ?: $attempt->assessment->material)
            ->filter(fn ($material) => $material && $material->is_published && (int) $material->class_id === $classId)
            ->unique('id')->values();
        $passingScore = (int) ($attempt->assessment->passing_score ?? 70);
        $passed = $attempt->score !== null && (float) $attempt->score >= $passingScore;
        $used = AssessmentAttempt::query()->where('assessment_id', $attempt->assessment_id)
            ->where('user_id', $request->user()->id)->count();
        $remaining = max(0, (int) $attempt->assessment->max_attempts - $used);
        // Percobaan yang sudah tuntas tidak perlu membuka remedial.
        $everPassed = AssessmentAttempt::query()->where('assessment_id', $attempt->assessment_id)
            ->where('user_id', $request->user()->id)
            ->whereNotNull('score')->where('score', '>=', $passingScore)->exists();
        $canRetry = !$everPassed && $remaining > 0 && QuizSchedule::canStart($attempt->assessment);
        return view('pages.siswa.quiz-result', compact(
            'attempt', 'remedialMaterials', 'passingScore', 'passed', 'remaining', 'canRetry'
        ));
    }
}
