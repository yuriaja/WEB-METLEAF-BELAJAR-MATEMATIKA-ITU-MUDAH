<?php
namespace App\Http\Controllers;
use App\Models\{SchoolClass,Material,Assessment,Question};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB,Storage};
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
class ClassroomController extends Controller {
 private function teacher():void {abort_unless(in_array(auth()->user()->role,['guru','admin'],true),403);}
 private function owned(int $id):SchoolClass { $this->teacher();return auth()->user()->teachingClasses()->findOrFail($id); }
 private function enrolled(int $id):bool {return auth()->user()->classes()->whereKey($id)->exists();}
 public function home(){
  $user=auth()->user();
  if(in_array($user->role,['guru','admin'],true)){
   $classes=$user->teachingClasses()->withCount('students')->latest()->get();
   $materials=Material::where('author_id',$user->id)->with('author')->latest()->get();
   $assessments=Assessment::where('author_id',$user->id)->withCount('questions')->latest()->get();
  } else {
   $classes=$user->classes()->with('teacher')->get();
   $ids=$classes->pluck('id');
   $materials=Material::where('is_published',true)->whereIn('class_id',$ids)->latest()->get();
   $assessments=Assessment::where('is_published',true)->whereIn('class_id',$ids)->withCount('questions')->latest()->get();
  }
  return view('pages.guru.classroom',compact('classes','materials','assessments'));
 }
 public function teacherDashboard(){
  $this->teacher();
  $user = auth()->user();
  $classes = $user->teachingClasses()->withCount('students')->latest()->get();
  $classIds = $classes->pluck('id')->all();
  $materials = Material::where('author_id', $user->id)->latest()->get();
  $assessments = Assessment::where('author_id', $user->id)->withCount('questions')->latest()->get();

  $pendingTasks = \App\Models\Submission::query()
      ->whereNull('score')
      ->whereHas('activity', fn ($query) => $query->where('mode', 'tugas')->whereIn('class_id', $classIds))
      ->count();

  // Angka forum menunjukkan jumlah topik aktual, bukan komentar demo.
  $forumThreadsCount = \App\Models\ForumThread::query()
      ->whereIn('class_id', $classIds)
      ->count();

  $submissions = \App\Models\Submission::query()
      ->with(['user:id,name', 'activity:id,title,class_id'])
      ->whereNotNull('user_id')
      ->whereHas('activity', fn ($query) => $query->where('mode', 'tugas')->whereIn('class_id', $classIds))
      ->latest('created_at')->limit(6)->get()
      ->map(fn ($item) => (object) [
          'date' => $item->submitted_at ?? $item->created_at,
          'actor' => $item->user?->name ?? 'Siswa',
          'action' => 'mengumpulkan tugas',
          'target' => $item->activity?->title ?? 'Tugas',
      ]);

  $quizAttempts = \App\Models\AssessmentAttempt::query()
      ->with(['user:id,name', 'assessment:id,title,class_id'])
      ->whereHas('assessment', fn ($query) => $query->whereIn('class_id', $classIds))
      ->latest('created_at')->limit(6)->get()
      ->map(fn ($item) => (object) [
          'date' => $item->submitted_at ?? $item->created_at,
          'actor' => $item->user?->name ?? 'Siswa',
          'action' => 'menyelesaikan kuis',
          'target' => $item->assessment?->title ?? 'Kuis',
      ]);

  $threads = \App\Models\ForumThread::query()
      ->with('author:id,name')
      ->whereIn('class_id', $classIds)
      ->latest()->limit(6)->get()
      ->map(fn ($item) => (object) [
          'date' => $item->created_at,
          'actor' => $item->author?->name ?? 'Pengguna',
          'action' => 'membuka diskusi',
          'target' => $item->title,
      ]);

  $recentActivities = $submissions->concat($quizAttempts)->concat($threads)
      ->sortByDesc('date')->take(6)->values();

  return view('pages.guru.dashboard', compact(
      'classes', 'materials', 'assessments',
      'pendingTasks', 'forumThreadsCount', 'recentActivities'
  ));
 }
 public function teacherQuizzes(){
  $this->teacher();
  $assessments=Assessment::where('author_id',auth()->id())->withCount('questions')->latest()->get();
  return view('pages.guru.classroom-quiz',compact('assessments'));
 }
 public function startQuiz(Request $r){
  $this->teacher();
  $v=$r->validate([
   'title'=>'required|string|max:255',
   'class_id'=>'required|integer',
   'type'=>'required|in:latihan,kuis,ujian',
   'time_limit_minutes'=>'required|integer|between:1,600',
  ]);
  $class=$this->owned((int)$v['class_id']);
  $assessment=Assessment::create([
   'author_id'=>$r->user()->id,
   'class_id'=>$class->id,
   'title'=>$v['title'],
   'type'=>$v['type'],
   'time_limit_minutes'=>$v['time_limit_minutes'],
   'is_published'=>false,
   'max_attempts'=>1,
  ]);
  return redirect()->route('teacher.quizzes.edit',$assessment);
 }
 public function editQuiz(Assessment $assessment){
  $this->teacher();
  abort_unless($assessment->author_id===auth()->id() || auth()->user()->role==='admin',403);
  $user=auth()->user();
  $classes=$user->role==='admin'
   ? SchoolClass::orderBy('name')->get()
   : $user->teachingClasses()->orderBy('name')->get();
  $assessment->load('questions');
  return view('pages.shared.see-quiz',compact('assessment','classes'));
 }
 public function saveQuiz(Request $r,Assessment $assessment){
  $this->teacher();
  abort_unless($assessment->author_id===$r->user()->id || $r->user()->role==='admin',403);
  abort_if($assessment->attempts()->exists(),403,'Kuis yang sudah dikerjakan tidak dapat diubah.');
  $v=$r->validate([
   'title'=>'required|string|max:255',
   'class_id'=>'required|integer',
   'type'=>'required|in:latihan,kuis,ujian',
   'time_limit_minutes'=>'required|integer|between:1,600',
   'questions'=>'required|array|min:1|max:50',
   'questions.*.text'=>'required|string|max:3000',
   'questions.*.options'=>'required|array|size:4',
   'questions.*.options.*'=>'required|string|max:1000',
   'questions.*.correct_answer'=>'required|integer|between:0,3',
   'questions.*.points'=>'required|integer|min:1|max:1000',
  ]);
  $class=$r->user()->role==='admin'
   ? SchoolClass::findOrFail($v['class_id'])
   : $this->owned((int)$v['class_id']);
  DB::transaction(function()use($assessment,$v,$class){
   $assessment->update([
    'title'=>$v['title'],
    'class_id'=>$class->id,
    'type'=>$v['type'],
    'time_limit_minutes'=>$v['time_limit_minutes'],
    'is_published'=>true,
   ]);
   $assessment->questions()->delete();
   foreach($v['questions'] as $index=>$question){
    $assessment->questions()->create([
     'question_text'=>$question['text'],
     'type'=>'multiple_choice',
     'options'=>$question['options'],
     'correct_answer'=>(string)$question['correct_answer'],
     'points'=>$question['points'],
     'sort_order'=>$index,
    ]);
   }
  });
  return redirect()->route('teacher.quizzes')->with('success','Kuis berhasil disimpan dan dipublikasikan.');
 }
 public function setActiveClass(Request $r){
  $v=$r->validate(['kelas_id'=>'nullable|integer']);
  $user=$r->user();
  $classId=$v['kelas_id']??null;
  if(empty($classId)){$r->session()->forget('kelas_aktif_id');return back();}
  $classes=in_array($user->role,['guru','admin'],true)?$user->teachingClasses():$user->classes();
  abort_unless($classes->whereKey($classId)->exists(),403);
  $r->session()->put('kelas_aktif_id',(int)$classId);
  return back();
 }
 public function createClass(Request $r){$this->teacher();$v=$r->validate(['name'=>'required|string|max:100','school_year'=>'nullable|string|max:15']);
  do{$code=Str::upper(Str::random(8));}while(SchoolClass::where('join_code',$code)->exists());
  auth()->user()->teachingClasses()->create($v+['join_code'=>$code]);return back()->with('success','Kelas berhasil dibuat. Bagikan kode kepada siswa.');
 }
 public function joinClass(Request $r){abort_unless($r->user()->role==='siswa',403);$v=$r->validate(['join_code'=>'required|string|max:12']);
  $class=SchoolClass::where('join_code',strtoupper(trim($v['join_code'])))->first();
  if(!$class)throw ValidationException::withMessages(['join_code'=>'Kode kelas tidak ditemukan.']);
  $class->students()->syncWithoutDetaching([$r->user()->id]);return back()->with('success','Berhasil bergabung ke '.$class->name);
 }
 public function upload(Request $r){$this->teacher();$v=$r->validate(['class_id'=>'required|integer','title'=>'required|string|max:255','description'=>'nullable|string|max:3000','type'=>'required|in:modul,video,ppt,foto,link','resource_url'=>'nullable|url|max:2048','file'=>'nullable|file|mimes:pdf,ppt,pptx,jpg,jpeg,png,webp,mp4,webm|max:51200','is_published'=>'nullable|boolean']);
  $class=$this->owned((int)$v['class_id']);
  if(!$r->hasFile('file')&&empty($v['resource_url']))throw ValidationException::withMessages(['file'=>'Unggah berkas atau isi URL materi.']);
  $path=$r->hasFile('file')?$r->file('file')->store('mathleaf/materials','local'):null;
  Material::create(['class_id'=>$class->id,'author_id'=>$r->user()->id,'title'=>$v['title'],'description'=>$v['description']??null,'slug'=>Str::slug($v['title']).'-'.Str::lower(Str::random(8)),'type'=>$v['type'],'resource_url'=>$v['resource_url']??null,'file_path'=>$path,'original_filename'=>$r->hasFile('file')?$r->file('file')->getClientOriginalName():null,'is_published'=>$r->boolean('is_published')]);
  return back()->with('success','Materi tersimpan untuk kelas '.$class->name);
 }
 public function toggleMaterial(Material $material){$this->teacher();abort_unless($material->author_id===auth()->id() && $material->class_id,403);$this->owned((int)$material->class_id);$material->update(['is_published'=>!$material->is_published]);return back()->with('success','Status publikasi diperbarui.');}
 public function file(Material $material){
  $user=auth()->user();abort_unless($material->class_id && ($material->author_id===$user->id || ($material->is_published && $this->enrolled($material->class_id))),403);
  abort_unless($material->file_path && Storage::disk('local')->exists($material->file_path),404);
  return Storage::disk('local')->download($material->file_path,$material->original_filename?:basename($material->file_path));
 }
 public function createQuiz(Request $r){$this->teacher();$v=$r->validate(['class_id'=>'required|integer','title'=>'required|string|max:255','type'=>'required|in:latihan,kuis,sumatif','questions'=>'required|array|min:1|max:50','questions.*.text'=>'required|string|max:3000','questions.*.options'=>'required|array|size:4','questions.*.options.*'=>'required|string|max:1000','questions.*.answer'=>'required|integer|between:0,3','questions.*.explanation'=>'nullable|string|max:3000']);$class=$this->owned((int)$v['class_id']);
  DB::transaction(function()use($v,$r,$class){$q=Assessment::create(['class_id'=>$class->id,'author_id'=>$r->user()->id,'title'=>$v['title'],'type'=>$v['type'],'is_published'=>true,'max_attempts'=>1]);foreach($v['questions'] as $i=>$item){$q->questions()->create(['question_text'=>$item['text'],'type'=>'multiple_choice','options'=>$item['options'],'correct_answer'=>(string)$item['answer'],'explanation'=>$item['explanation']??null,'points'=>10,'sort_order'=>$i]);}});
  return back()->with('success','Soal dipublikasikan ke kelas '.$class->name);
 }
 public function takeQuiz(Assessment $assessment){$user=auth()->user();abort_unless($assessment->is_published && $assessment->class_id && $this->enrolled($assessment->class_id),403);$assessment->load('questions');$already=$user->attempts()->where('assessment_id',$assessment->id)->count();return view('pages.guru.classroom-quiz',compact('assessment','already'));}
 public function submitQuiz(Request $r,Assessment $assessment){abort_unless($r->user()->role==='siswa' && $assessment->is_published && $assessment->class_id && $this->enrolled($assessment->class_id),403);$r->validate(['answers'=>'required|array']);
  $already=$r->user()->attempts()->where('assessment_id',$assessment->id)->count();abort_if($already >= $assessment->max_attempts,403,'Percobaan sudah habis.');
  $questions=$assessment->questions()->get();$total=$questions->sum('points');$correct=0;
  $attempt=DB::transaction(function()use($r,$assessment,$questions,$already,$total,&$correct){$attempt=$assessment->attempts()->create(['user_id'=>$r->user()->id,'attempt_number'=>$already+1,'status'=>'graded','submitted_at'=>now(),'score'=>0]);$points=0;foreach($questions as $q){$answer=(string)($r->input('answers.'.$q->id,'-1'));$isCorrect=$answer===(string)$q->correct_answer;if($isCorrect){$correct++;$points+=$q->points;}$attempt->answers()->create(['question_id'=>$q->id,'answer_text'=>$answer,'is_correct'=>$isCorrect,'points_awarded'=>$isCorrect?$q->points:0,'feedback'=>$q->explanation]);}$attempt->update(['score'=>$total?round($points/$total*100,2):0]);return $attempt;});
  return redirect()->route('classroom.quiz.take',$assessment)->with('success','Kuis terkirim! Nilai: '.$attempt->score.' / 100 ('.$correct.' jawaban benar).');
 }
}
