<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StudentQuizController extends Controller
{
    private function student(Request $request): void
    {
        abort_unless($request->user()?->role === 'siswa', 403);
    }

    private function availableAssessment(Request $request, Assessment $assessment): Assessment
    {
        $this->student($request);

        $allowed = $assessment->is_published
            && $assessment->class_id !== null
            && $request->user()->classes()->whereKey($assessment->class_id)->exists();

        abort_unless($allowed, 403);

        return $assessment;
    }

    public function index(Request $request)
    {
        $this->student($request);
        $user = $request->user();
        $classIds = $user->classes()->pluck('classes.id');

        $assessments = Assessment::query()
            ->whereIn('class_id', $classIds)
            ->where('is_published', true)
            ->whereHas('questions')
            ->withCount('questions')
            ->latest()
            ->get();

        $attempts = AssessmentAttempt::query()
            ->where('user_id', $user->id)
            ->whereHas('assessment', fn ($query) => $query->whereIn('class_id', $classIds))
            ->with('assessment:id,title,type,class_id')
            ->orderByDesc('submitted_at')
            ->get();

        $attemptCounts = $attempts->countBy('assessment_id');
        $available = $assessments->filter(fn ($assessment) =>
            ($attemptCounts->get($assessment->id, 0)) < max(1, (int) $assessment->max_attempts)
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
            abort_unless($assessment->is_published, 403);

            $used = AssessmentAttempt::query()
                ->where('assessment_id', $assessment->id)
                ->where('user_id', $request->user()->id)
                ->count();

            abort_if($used >= max(1, (int) $assessment->max_attempts), 403, 'Kesempatan kuis sudah habis.');

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

        $attempt->load(['assessment', 'answers.question']);
        abort_unless(
            $attempt->assessment?->class_id !== null
                && $request->user()->classes()->whereKey($attempt->assessment->class_id)->exists(),
            403
        );

        return view('pages.siswa.quiz-result', compact('attempt'));
    }
}
