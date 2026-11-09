<?php
namespace App\Http\Controllers;

use App\Models\{User, SchoolClass, Material, MaterialProgress, Activity, Assessment, AssessmentAttempt, AssessmentAnswer, Question, Submission, ForumThread, ForumComment, StudyGroup};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Hash, Storage};
use Illuminate\Validation\ValidationException;
use App\Support\ForumAccess;
use App\Support\QuizSchedule;

class MathLeafController extends Controller
{
    private function teacher(Request $r): void { abort_unless(in_array($r->user()->role, ['guru','admin'], true), 403, 'Khusus guru'); }
    private function classIds(Request $r): array { return $r->user()->classes()->pluck('classes.id')->all(); }
    private function belongsToClass(Request $r, ?int $classId): void {
        if (!$classId) return;
        abort_unless($r->user()->role === 'admin' || $r->user()->teachingClasses()->whereKey($classId)->exists() || $r->user()->classes()->whereKey($classId)->exists(), 403);
    }
    public function register(Request $r) {
        $v=$r->validate(['name'=>'required|string|max:255','email'=>'required|email|unique:users,email','password'=>'required|string|min:8','role'=>'required|in:siswa,guru']);
        // Role guru bersifat demo/lokal; untuk produksi wajib verifikasi admin sebelum akun guru aktif.
        $user=DB::transaction(function() use ($v) {
            $u=User::create(['name'=>$v['name'],'email'=>$v['email'],'password'=>$v['password'],'role'=>$v['role']]);
            return $u;
        }, 3);
        // Siswa tidak langsung masuk setelah registrasi; kelas dipilih saat pertama kali login.
        if ($user->role === 'siswa') {
            return response()->json(['user' => $user->only('id', 'name', 'email', 'role'), 'redirect' => '/masuk?registered=1'], 201);
        }
        Auth::login($user);
        $r->session()->regenerate();
        return response()->json(['user' => $user->only('id', 'name', 'email', 'role'), 'redirect' => '/guru'], 201);
    }
    public function login(Request $r) {
        // Siswa wajib memasukkan kode kelas 8 karakter setiap login.
        $r->merge(['kode_kelas' => strtoupper(trim((string) $r->input('kode_kelas', '')))]);
        $v = $r->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'role' => 'required|in:siswa,guru',
            'kode_kelas' => 'required_if:role,siswa|nullable|string|size:8|alpha_num',
        ], [
            'kode_kelas.required_if' => 'Kode kelas Matematika wajib diisi setiap siswa login.',
            'kode_kelas.size' => 'Kode kelas harus tepat 8 karakter.',
        ]);
        if (!Auth::attempt(['email' => $v['email'], 'password' => $v['password'], 'role' => $v['role']])) {
            throw ValidationException::withMessages(['email' => 'Email, kata sandi, atau peran salah.']);
        }
        if ($r->user()->role === 'siswa') {
            try {
                $classId = DB::transaction(function () use ($r, $v) {
                    $student = User::query()->whereKey($r->user()->id)->lockForUpdate()->firstOrFail();
                    $enrolledIds = $student->classes()->pluck('classes.id')->map(fn ($id) => (int) $id)->all();
                    if (count($enrolledIds) > 1) {
                        throw ValidationException::withMessages(['kode_kelas' => 'Akun sudah tercatat di beberapa kelas lama. Hubungi guru untuk menyesuaikan ke satu kelas Matematika.']);
                    }

                    if ($enrolledIds) {
                        $class = SchoolClass::query()->whereKey($enrolledIds[0])->lockForUpdate()->firstOrFail();
                        if ($v['kode_kelas'] !== $class->join_code) {
                            throw ValidationException::withMessages(['kode_kelas' => 'Kamu sudah terdaftar di satu kelas Matematika lain. Hubungi guru jika perlu pindah kelas.']);
                        }
                    } else {
                        if ($v['kode_kelas'] === '') {
                            throw ValidationException::withMessages(['kode_kelas' => 'Masukkan kode kelas dari guru untuk bergabung pertama kali.']);
                        }
                        $class = SchoolClass::query()->where('join_code', $v['kode_kelas'])->lockForUpdate()->first();
                        if (!$class) {
                            throw ValidationException::withMessages(['kode_kelas' => 'Kode kelas tidak ditemukan. Periksa kode dari guru.']);
                        }
                    }
                    if (DB::table('class_kicked_users')->where('class_id', $class->id)->where('user_id', $student->id)->exists()) {
                        throw ValidationException::withMessages(['kode_kelas' => 'Kamu telah dikeluarkan dari kelas ini. Minta guru membuka izin bergabung kembali.']);
                    }
                    if (!$enrolledIds) {
                        $count = $class->students()->count();
                        if ($count >= (int) $class->max_students) {
                            throw ValidationException::withMessages(['kode_kelas' => 'Kelas Matematika ini sudah penuh ('.$class->max_students.' siswa).']);
                        }
                        $class->students()->attach($student->id);
                    }
                    return (int) $class->id;
                }, 3);
                $r->session()->put('kelas_aktif_id', $classId);
            } catch (\Throwable $e) {
                // Biarkan form AJAX mencoba lagi setelah siswa memasukkan kode yang benar.
                Auth::logout();
                throw $e;
            }
        }
        $r->session()->regenerate();
        $redirect = $r->user()->role === 'guru' ? '/guru' : '/dashboard';
        if ($r->expectsJson()) return response()->json(['user' => $r->user()->only('id', 'name', 'email', 'role'), 'redirect' => $redirect]);
        return redirect()->to($redirect);
    }
    public function logout(Request $r) {
        Auth::logout();$r->session()->invalidate();$r->session()->regenerateToken();
        return $r->expectsJson()
            ? response()->json(['ok'=>true])
            : redirect()->route('login');
    }
    public function me(Request $r) { return response()->json($r->user()->only('id','name','email','role')); }
    public function overview(Request $r) {
        $u=$r->user();$attempts=$u->attempts()->whereNotNull('score');
        $scores=$attempts->pluck('score');$total=Material::where('is_published',true)->count();
        $done=$u->progress()->where('progress_percent',100)->count();
        return response()->json(['name'=>$u->name,'role'=>$u->role,'mean_score'=>$scores->count()?round($scores->avg(),1):0,'completed'=>$done,'total_materials'=>$total,'progress_percent'=>$total?round($done/$total*100):0,'submitted'=>$u->submissions()->count(),'attempts'=>$u->attempts()->with('assessment')->latest()->take(20)->get()]);
    }
    public function materials(Request $r) {
        $q=Material::query();if($r->user()->role==='siswa') $q->where('is_published',true)->where(function($query) use($r) { $query->whereNull('class_id')->orWhereIn('class_id',$this->classIds($r)); });
        return response()->json($q->with(['progress'=>fn($q)=>$q->where('user_id',$r->user()->id)])->orderBy('sort_order')->get());
    }
    public function completeMaterial(Request $r, Material $material) {
        abort_unless($material->is_published,404);
        $v=$r->validate(['progress_percent'=>'required|integer|between:0,100']);
        $row=MaterialProgress::updateOrCreate(['material_id'=>$material->id,'user_id'=>$r->user()->id],['progress_percent'=>$v['progress_percent'],'completed_at'=>$v['progress_percent']===100?now():null]);
        return response()->json($row);
    }
    public function activities(Request $r) {
        $q=Activity::with('material')->orderBy('fpbl_stage');
        if($r->user()->role==='siswa') $q->where(fn($q)=>$q->whereNull('class_id')->orWhereIn('class_id',$this->classIds($r)));
        return response()->json($q->get());
    }
    public function storeActivity(Request $r) {
        $this->teacher($r);$v=$r->validate(['title'=>'required|string|max:255','fpbl_stage'=>'required|integer|between:1,4','mode'=>'required|in:asinkron,sinkron','instructions'=>'nullable|string','class_id'=>'nullable|exists:classes,id','material_id'=>'nullable|exists:materials,id','due_at'=>'nullable|date']);
        $this->belongsToClass($r,$v['class_id']??null);return response()->json(Activity::create($v),201);
    }
    public function assessments(Request $r) {
        QuizSchedule::archiveExpired();
        $q=Assessment::withCount('questions');if($r->user()->role==='siswa')$q->where('is_published',true)->whereNull('archived_at')->where(fn($query)=>$query->whereNull('starts_at')->orWhere('starts_at','<=',now()))->where(fn($query)=>$query->whereNull('due_at')->orWhere('due_at','>',now()))->where(function($query) use($r) { $query->whereNull('class_id')->orWhereIn('class_id',$this->classIds($r)); });
        return response()->json($q->get());
    }
    public function questions(Request $r,Assessment $assessment) {
        if ($r->user()->role === 'siswa') {
            QuizSchedule::archiveExpired();
            $assessment->refresh();
            // Hide unpublished, archived, and foreign-class quizzes before schedule checks.
            abort_if(
                !$assessment->is_published
                || $assessment->archived_at !== null
                || ($assessment->class_id && !in_array((int) $assessment->class_id, array_map('intval', $this->classIds($r)), true)),
                404
            );
            QuizSchedule::assertOpen($assessment);
        }
        $q=$assessment->questions()->get();
        if($r->user()->role==='siswa')$q->makeHidden(['correct_answer','explanation']);
        return response()->json(['assessment'=>$assessment->only('id','title','type','max_attempts','passing_score','time_limit_minutes'),'questions'=>$q]);
    }
    public function submitAssessment(Request $r,Assessment $assessment) {
        QuizSchedule::archiveExpired();
        $assessment->refresh();
        // Endpoint API mengikuti aturan yang sama dengan halaman kuis siswa.
        abort_unless($r->user()->role === 'siswa', 403);
        QuizSchedule::assertOpen($assessment);
        abort_unless($assessment->class_id, 403);
        abort_unless(in_array((int) $assessment->class_id, array_map('intval', $this->classIds($r)), true), 403);
        $v = $r->validate([
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|integer',
            'answers.*.answer' => 'nullable|string|max:10000',
        ]);

        return DB::transaction(function () use ($r, $assessment, $v) {
            $assessment = Assessment::query()->lockForUpdate()->findOrFail($assessment->id);
            QuizSchedule::assertOpen($assessment);
            $u = $r->user();
            $count = AssessmentAttempt::query()
                ->where('assessment_id', $assessment->id)->where('user_id', $u->id)->count();
            abort_if($count >= max(1, (int) $assessment->max_attempts), 422, 'Batas percobaan habis.');
            $alreadyPassed = AssessmentAttempt::query()
                ->where('assessment_id', $assessment->id)->where('user_id', $u->id)
                ->whereNotNull('score')->where('score', '>=', (int) ($assessment->passing_score ?? 70))->exists();
            abort_if($alreadyPassed, 422, 'Kuis sudah tuntas; remedial tidak diperlukan.');

            $questions = $assessment->questions()->get()->keyBy('id');
            $submitted = collect($v['answers'])->keyBy('question_id');
            $expected = $questions->keys()->map(fn ($id) => (string) $id)->sort()->values()->all();
            $provided = $submitted->keys()->map(fn ($id) => (string) $id)->sort()->values()->all();
            if ($expected !== $provided || $questions->isEmpty() || count($v['answers']) !== $submitted->count()) {
                throw ValidationException::withMessages(['answers' => 'Jawab setiap soal tepat satu kali.']);
            }

            $max = $questions->sum('points'); $total = 0; $manual = false;
            $attempt = AssessmentAttempt::create([
                'assessment_id' => $assessment->id, 'user_id' => $u->id,
                'attempt_number' => $count + 1, 'status' => 'submitted', 'submitted_at' => now(),
            ]);
            foreach ($questions as $q) {
                $answer = (string) ($submitted->get($q->id)['answer'] ?? '');
                $automatic = in_array($q->type, ['multiple_choice', 'short_answer'], true);
                $correct = $automatic && mb_strtolower(trim($answer)) === mb_strtolower(trim((string) $q->correct_answer));
                $awarded = $automatic ? ($correct ? $q->points : 0) : null;
                if (! $automatic) { $manual = true; }
                $total += $awarded ?? 0;
                AssessmentAnswer::create([
                    'assessment_attempt_id' => $attempt->id, 'question_id' => $q->id,
                    'answer_text' => $answer, 'is_correct' => $automatic ? $correct : null,
                    'points_awarded' => $awarded, 'feedback' => $automatic ? $q->explanation : null,
                ]);
            }
            $attempt->update([
                'score' => $manual ? null : ($max ? round($total / $max * 100, 2) : 0),
                'status' => $manual ? 'needs_grading' : 'graded',
            ]);
            return response()->json([
                'attempt_id' => $attempt->id, 'score' => $attempt->score, 'status' => $attempt->status,
                'passing_score' => $assessment->passing_score ?? 70,
                'correct' => $attempt->answers()->where('is_correct', true)->count(),
                'total' => $questions->count(),
                'review' => $attempt->answers()->with('question')->get()->map(fn ($a) => [
                    'question' => $a->question?->question_text, 'answer' => $a->answer_text,
                    'correct_answer' => $a->question?->correct_answer, 'explanation' => $a->feedback,
                    'is_correct' => $a->is_correct,
                ]),
            ], 201);
        }, 3);
    }
    public function submissions(Request $r) {
        $q=Submission::with(['activity','user','group']);if($r->user()->role==='siswa') $q->where('user_id',$r->user()->id);
        return response()->json($q->latest()->get());
    }
    public function storeSubmission(Request $r) {
        $v=$r->validate(['activity_id'=>'required|exists:activities,id','content'=>'nullable|string|max:20000','file'=>'nullable|file|max:10240','study_group_id'=>'nullable|exists:study_groups,id']);
        abort_if(empty($v['content']) && !$r->hasFile('file'),422,'Isi jawaban atau unggah berkas.');
        $activity=Activity::findOrFail($v['activity_id']);$this->belongsToClass($r,$activity->class_id);
        if(!empty($v['study_group_id'])) abort_unless(StudyGroup::findOrFail($v['study_group_id'])->members()->whereKey($r->user()->id)->exists(),403);
        $path=$r->hasFile('file')?$r->file('file')->store('submissions','local'):null;
        return response()->json(Submission::create(['activity_id'=>$activity->id,'user_id'=>$r->user()->id,'study_group_id'=>$v['study_group_id']??null,'content'=>$v['content']??null,'file_path'=>$path,'submitted_at'=>now()]),201);
    }
    public function grade(Request $r,Submission $submission) {
        $this->teacher($r);$this->belongsToClass($r,$submission->activity->class_id);
        $v=$r->validate(['score'=>'required|numeric|between:0,100','teacher_feedback'=>'nullable|string|max:20000']);
        $submission->update($v+['graded_by'=>$r->user()->id]);return response()->json($submission);
    }
    public function download(Request $r,Submission $submission) {
        abort_unless($submission->user_id===$r->user()->id || in_array($r->user()->role,['guru','admin']),403);
        if($r->user()->role==='guru')$this->belongsToClass($r,$submission->activity->class_id);
        abort_unless($submission->file_path && Storage::disk('local')->exists($submission->file_path),404);
        return Storage::disk('local')->download($submission->file_path);
    }
    // Forum API: gunakan batasan kelas yang sama dengan halaman /forum.
    public function threads(Request $r) {
        return response()->json(
            ForumThread::with(['author:id,name,role', 'comments.author:id,name,role'])
                ->whereIn('class_id', ForumAccess::classIds($r->user()))
                ->latest()
                ->get()
        );
    }
    public function newThread(Request $r) {
        $v = $r->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:10000',
            'class_id' => 'required|integer|exists:classes,id',
            'activity_id' => 'nullable|integer|exists:activities,id',
        ]);
        abort_unless(ForumAccess::canAccess($r->user(), (int) $v['class_id']), 403);
        if (!empty($v['activity_id'])) {
            abort_unless(
                Activity::whereKey($v['activity_id'])->where('class_id', $v['class_id'])->exists(),
                422,
                'Aktivitas harus berasal dari kelas yang dipilih.'
            );
        }
        return response()->json(ForumThread::create($v + ['user_id' => $r->user()->id]), 201);
    }
    public function comment(Request $r, ForumThread $thread) {
        abort_unless(ForumAccess::canAccess($r->user(), $thread->class_id), 403);
        abort_if($thread->is_locked, 403, 'Diskusi ini telah dikunci oleh guru.');
        $v = $r->validate(['body' => 'required|string|max:10000']);
        return response()->json(ForumComment::create([
            'forum_thread_id' => $thread->id,
            'user_id' => $r->user()->id,
            'body' => $v['body'],
        ]), 201);
    }
    public function teacherOverview(Request $r) {
        $this->teacher($r);$ids=$r->user()->teachingClasses()->pluck('id');
        return response()->json(['classes'=>SchoolClass::whereIn('id',$ids)->withCount('students')->get(),'students'=>User::whereHas('classes',fn($q)=>$q->whereIn('classes.id',$ids))->get(['id','name','email']),'submissions'=>Submission::whereHas('activity',fn($q)=>$q->whereIn('class_id',$ids))->with(['user','activity'])->latest()->get(),'materials'=>Material::orderBy('sort_order')->get()]);
    }
    public function newMaterial(Request $r) {
        $this->teacher($r);$v=$r->validate(['title'=>'required|max:255','description'=>'nullable|string','type'=>'required|in:modul,video,link','category'=>'nullable|string','content'=>'nullable|string','resource_url'=>'nullable|url','is_published'=>'boolean']);
        $v['slug']=\Illuminate\Support\Str::slug($v['title']).'-'.\Illuminate\Support\Str::lower(\Illuminate\Support\Str::random(5));
        return response()->json(Material::create($v+['author_id'=>$r->user()->id]),201);
    }
    public function publishMaterial(Request $r,Material $material) {$this->teacher($r);$v=$r->validate(['is_published'=>'required|boolean']);$material->update($v);return response()->json($material);}
}
