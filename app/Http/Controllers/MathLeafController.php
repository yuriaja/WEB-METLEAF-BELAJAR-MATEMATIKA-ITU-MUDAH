<?php
namespace App\Http\Controllers;

use App\Models\{User, SchoolClass, Material, MaterialProgress, Activity, Assessment, AssessmentAttempt, AssessmentAnswer, Question, Submission, ForumThread, ForumComment, StudyGroup};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Hash, Storage};
use Illuminate\Validation\ValidationException;

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
        });
        Auth::login($user); $r->session()->regenerate();
        return response()->json(['user'=>$user->only('id','name','email','role'),'redirect'=>$user->role==='guru'?'/guru':'/dashboard'],201);
    }
    public function login(Request $r) {
        $v=$r->validate([
            'email'=>'required|email',
            'password'=>'required|string',
            'role'=>'required|in:siswa,guru',
            'kode_kelas'=>'nullable|string|max:12',
        ]);
        if(!Auth::attempt(['email'=>$v['email'],'password'=>$v['password'],'role'=>$v['role']])) throw ValidationException::withMessages(['email'=>'Email, kata sandi, atau peran salah.']);
        if($r->user()->role==='siswa' && filled($v['kode_kelas']??null)) {
            $class=SchoolClass::where('join_code',strtoupper(trim($v['kode_kelas'])))->first();
            if(!$class) {
                Auth::logout();
                throw ValidationException::withMessages(['kode_kelas'=>'Kode kelas tidak ditemukan. Periksa kembali kode dari gurumu.']);
            }
            $class->students()->syncWithoutDetaching([$r->user()->id]);
        }
        $r->session()->regenerate();
        $redirect=$r->user()->role==='guru'?'/guru':'/dashboard';
        if($r->expectsJson()) return response()->json(['user'=>$r->user()->only('id','name','email','role'),'redirect'=>$redirect]);
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
        $q=Assessment::withCount('questions');if($r->user()->role==='siswa')$q->where('is_published',true)->where(function($query) use($r) { $query->whereNull('class_id')->orWhereIn('class_id',$this->classIds($r)); });
        return response()->json($q->get());
    }
    public function questions(Request $r,Assessment $assessment) {
        abort_if($r->user()->role==='siswa' && (!$assessment->is_published || ($assessment->class_id && !in_array($assessment->class_id,$this->classIds($r)))),404);
        $q=$assessment->questions()->get();
        if($r->user()->role==='siswa')$q->makeHidden(['correct_answer','explanation']);
        return response()->json(['assessment'=>$assessment->only('id','title','type','max_attempts','time_limit_minutes'),'questions'=>$q]);
    }
    public function submitAssessment(Request $r,Assessment $assessment) {
        abort_unless($assessment->is_published,404); abort_if($assessment->class_id && !in_array($assessment->class_id,$this->classIds($r)) && $r->user()->role==='siswa',403);
        $v=$r->validate(['answers'=>'required|array','answers.*.question_id'=>'required|integer','answers.*.answer'=>'nullable|string|max:10000']);
        return DB::transaction(function() use($r,$assessment,$v){
            $u=$r->user();$count=AssessmentAttempt::where('assessment_id',$assessment->id)->where('user_id',$u->id)->lockForUpdate()->count();
            abort_if($count >= $assessment->max_attempts,422,'Batas percobaan habis');
            $questions=$assessment->questions()->get()->keyBy('id');$submitted=collect($v['answers'])->keyBy('question_id');$max=$questions->sum('points');$total=0;$manual=false;
            $attempt=AssessmentAttempt::create(['assessment_id'=>$assessment->id,'user_id'=>$u->id,'attempt_number'=>$count+1,'status'=>'submitted','submitted_at'=>now()]);
            foreach($questions as $q){
                $answer=(string)($submitted->get($q->id)['answer']??'');$automatic=in_array($q->type,['multiple_choice','short_answer']);
                $correct=$automatic && mb_strtolower(trim($answer))===mb_strtolower(trim((string)$q->correct_answer));
                $awarded=$automatic?($correct?$q->points:0):null;if(!$automatic)$manual=true;$total+=$awarded??0;
                AssessmentAnswer::create(['assessment_attempt_id'=>$attempt->id,'question_id'=>$q->id,'answer_text'=>$answer,'is_correct'=>$automatic?$correct:null,'points_awarded'=>$awarded,'feedback'=>$automatic?$q->explanation:null]);
            }
            $attempt->update(['score'=>$manual?null:($max?round($total/$max*100,2):0),'status'=>$manual?'needs_grading':'graded']);
            return response()->json(['attempt_id'=>$attempt->id,'score'=>$attempt->score,'status'=>$attempt->status,'correct'=>$attempt->answers()->where('is_correct',true)->count(),'total'=>$questions->count(),'review'=>$attempt->answers()->with('question')->get()->map(fn($a)=>['question'=>$a->question->question_text,'answer'=>$a->answer_text,'correct_answer'=>$a->question->correct_answer,'explanation'=>$a->feedback,'is_correct'=>$a->is_correct])],201);
        });
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
    public function threads(Request $r) {
        return response()->json(ForumThread::with(['author:id,name,role','comments.author:id,name,role'])->where(fn($q)=>$q->whereNull('class_id')->orWhereIn('class_id',$this->classIds($r)))->latest()->get());
    }
    public function newThread(Request $r) {
        $v=$r->validate(['title'=>'required|string|max:255','body'=>'required|string|max:10000','class_id'=>'nullable|exists:classes,id','activity_id'=>'nullable|exists:activities,id']);
        $this->belongsToClass($r,$v['class_id']??null);
        return response()->json(ForumThread::create($v+['user_id'=>$r->user()->id]),201);
    }
    public function comment(Request $r,ForumThread $thread) {
        $this->belongsToClass($r,$thread->class_id);$v=$r->validate(['body'=>'required|string|max:10000']);
        return response()->json(ForumComment::create(['forum_thread_id'=>$thread->id,'user_id'=>$r->user()->id,'body'=>$v['body']]),201);
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
