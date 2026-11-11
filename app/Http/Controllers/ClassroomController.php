<?php
namespace App\Http\Controllers;
use App\Models\{SchoolClass,Material,Assessment,Question,AssessmentAttempt,ForumComment,ForumThread};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB,Storage};
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Support\QuizSchedule;
class ClassroomController extends Controller {
 private function teacher():void {abort_unless(in_array(auth()->user()->role,['guru','admin'],true),403);}
 private function owned(int $id):SchoolClass { $this->teacher();return auth()->user()->teachingClasses()->findOrFail($id); }
 private function enrolled(int $id):bool {return auth()->user()->classes()->whereKey($id)->exists();}
 public function home(){
  $user=auth()->user();
  if(in_array($user->role,['guru','admin'],true)){
   $classes=$user->teachingClasses()->withCount('students')->latest()->get();
   $materials=Material::where('author_id',$user->id)->with('author')->latest()->get();
   $assessments=Assessment::where('author_id',$user->id)->whereNull('archived_at')->withCount('questions')->latest()->get();
  } else {
   $classes=$user->classes()->with('teacher')->get();
   $ids=$classes->pluck('id');
   $materials=Material::where('is_published',true)->whereIn('class_id',$ids)->latest()->get();
   $assessments=Assessment::where('is_published',true)->whereNull('archived_at')->whereIn('class_id',$ids)->withCount('questions')->latest()->get();
  }
  return view('pages.guru.classroom',compact('classes','materials','assessments'));
 }
 public function teacherDashboard(){
  $this->teacher();
  $user = auth()->user();
  $classes = $user->teachingClasses()->withCount('students')->latest()->get();
  $classIds = $classes->pluck('id')->all();
  $materials = Material::where('author_id', $user->id)->latest()->get();
  $assessments = Assessment::where('author_id', $user->id)->whereNull('archived_at')->withCount('questions')->latest()->get();

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
          'url' => $item->activity
              ? route('classroom.tasks.index', [
                  'class_id' => $item->activity->class_id,
                  'submission' => $item->id,
              ])
              : route('classroom.tasks.index'),
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
          'url' => $item->assessment
              ? route('teacher.quizzes.attempt', $item)
              : route('teacher.quizzes'),
      ]);

  $threads = ForumThread::query()
      ->with('author:id,name')
      ->whereIn('class_id', $classIds)
      ->whereHas('author', fn ($query) => $query->where('role', 'siswa'))
      ->latest()->limit(6)->get()
      ->map(fn ($item) => (object) [
          'date' => $item->created_at,
          'actor' => $item->author?->name ?? 'Pengguna',
          'action' => 'membuat topik diskusi',
          'target' => $item->title,
          'url' => route('forum.index', ['class_id' => $item->class_id, 'thread' => $item->id]),
      ]);

  $forumReplies = ForumComment::query()
      ->with(['author:id,name', 'forumThread:id,title,class_id'])
      ->whereHas('author', fn ($query) => $query->where('role', 'siswa'))
      ->whereHas('forumThread', fn ($query) => $query->whereIn('class_id', $classIds))
      ->latest('created_at')->limit(6)->get()
      ->map(fn ($item) => (object) [
          'date' => $item->created_at,
          'actor' => $item->author?->name ?? 'Siswa',
          'action' => 'membalas diskusi',
          'target' => $item->forumThread?->title ?? 'Diskusi',
          'url' => $item->forumThread
              ? route('forum.index', [
                  'class_id' => $item->forumThread->class_id,
                  'thread' => $item->forum_thread_id,
              ])
              : route('forum.index'),
      ]);

  $recentActivities = $submissions->concat($quizAttempts)->concat($threads)->concat($forumReplies)
      ->sortByDesc('date')->take(6)->values();

  $publishedMaterialCount = Material::query()->whereIn('class_id', $classIds)
      ->where('is_published', true)->count();
  $publishedQuizCount = Assessment::query()->whereIn('class_id', $classIds)
      ->where('is_published', true)->whereNull('archived_at')->count();
  $totalTasks = \App\Models\Activity::query()->where('mode', 'tugas')
      ->whereIn('class_id', $classIds)->count();
  $averageQuizScore = \App\Models\AssessmentAttempt::query()
      ->whereNotNull('score')
      ->whereHas('assessment', fn ($query) => $query->whereIn('class_id', $classIds))
      ->avg('score');
  $upcomingTaskDeadlines = \App\Models\Activity::query()
      ->where('mode', 'tugas')->whereIn('class_id', $classIds)
      ->whereNotNull('due_at')->where('due_at', '>=', now())
      ->orderBy('due_at')->limit(4)->get(['id', 'title', 'due_at', 'class_id']);
  $upcomingTaskDeadlines->each(fn ($task) => $task->url = route('classroom.tasks.edit', $task));
  $uniqueStudentCount = \Illuminate\Support\Facades\DB::table('class_user')
      ->whereIn('class_id', $classIds)->distinct()->count('user_id');

  return view('pages.guru.dashboard', compact(
      'classes', 'materials', 'assessments',
      'pendingTasks', 'forumThreadsCount', 'recentActivities',
      'publishedMaterialCount', 'publishedQuizCount', 'totalTasks', 'averageQuizScore',
      'upcomingTaskDeadlines', 'uniqueStudentCount'
  ));
 }
 public function materialsPage(){
  $materials=auth()->user()->role==='guru'
   ? Material::where('author_id',auth()->id())->whereNotNull('class_id')->orderBy('sort_order')->latest()->get()
   : null;
  return view('pages.shared.materi',compact('materials'));
 }
 public function createMaterial(){
  $this->teacher();
  return view('pages.shared.materi-form',['material'=>null]);
 }
 public function editMaterial(Material $material){
  $this->authorizeMaterial($material);
  return view('pages.shared.materi-form',compact('material'));
 }
 public function storeMaterial(Request $r){
  $this->teacher();
  $v=$this->validateMaterial($r);
  $class=$this->owned((int)$v['class_id']);
  $path=$r->hasFile('file')?$r->file('file')->store('mathleaf/materials','local'):null;
  $material=Material::create([
   'class_id'=>$class->id,
   'author_id'=>$r->user()->id,
   'title'=>$v['title'],
   'slug'=>$this->uniqueMaterialSlug($v['slug']??'', $v['title']),
   'description'=>$v['description']??null,
   'category'=>$v['category']??null,
   'type'=>$v['type'],
   'content'=>$v['content']??null,
   'resource_url'=>$v['resource_url']??null,
   'file_path'=>$path,
   'original_filename'=>$r->hasFile('file')?$r->file('file')->getClientOriginalName():null,
   'duration_minutes'=>$v['duration_minutes']??null,
   'sort_order'=>$v['sort_order']??0,
   'is_published'=>$r->boolean('is_published'),
  ]);
  return redirect()->route('teacher.materials.edit',$material)->with('success','Materi berhasil disimpan.');
 }
 public function updateMaterial(Request $r,Material $material){
  $this->authorizeMaterial($material);
  $v=$this->validateMaterial($r);
  $class=$this->owned((int)$v['class_id']);
  $oldPath=$material->file_path;
  $newPath=$r->hasFile('file')?$r->file('file')->store('mathleaf/materials','local'):null;
  $material->update([
   'class_id'=>$class->id,
   'title'=>$v['title'],
   'slug'=>$this->uniqueMaterialSlug($v['slug']??'', $v['title'], $material),
   'description'=>$v['description']??null,
   'category'=>$v['category']??null,
   'type'=>$v['type'],
   'content'=>$v['content']??null,
   'resource_url'=>$v['resource_url']??null,
   'duration_minutes'=>$v['duration_minutes']??null,
   'sort_order'=>$v['sort_order']??0,
   'is_published'=>$r->boolean('is_published'),
   'file_path'=>$newPath??($r->boolean('remove_file')?null:$oldPath),
   'original_filename'=>$newPath?$r->file('file')->getClientOriginalName():($r->boolean('remove_file')?null:$material->original_filename),
  ]);
  if(($newPath||$r->boolean('remove_file'))&&$oldPath)Storage::disk('local')->delete($oldPath);
  return redirect()->route('teacher.materials.edit',$material)->with('success','Materi berhasil diperbarui.');
 }
 private function validateMaterial(Request $r):array {
  return $r->validate([
   'class_id'=>'required|integer',
   'title'=>'required|string|max:255',
   'slug'=>'nullable|string|max:255',
   'description'=>'nullable|string|max:300',
   'category'=>'nullable|string|max:255',
   'type'=>'required|in:modul,video,dokumen',
   'content'=>'nullable|string|max:5000000',
   'resource_url'=>'nullable|url|max:255',
   'file'=>'nullable|file|mimes:pdf,ppt,pptx,doc,docx|max:20480',
   'remove_file'=>'nullable|boolean',
   'is_published'=>'nullable|boolean',
   'duration_minutes'=>'nullable|integer|between:1,600',
   'sort_order'=>'nullable|integer|min:0',
  ]);
 }
 private function authorizeMaterial(Material $material):void {
  $this->teacher();
  abort_unless($material->author_id===auth()->id()&&$material->class_id,403);
  $this->owned((int)$material->class_id);
 }
 private function uniqueMaterialSlug(string $slug,string $title,?Material $material=null):string {
  $base=Str::slug($slug?:$title)?:'materi';
  $candidate=$base;
  $suffix=2;
  while(Material::where('slug',$candidate)->when($material,fn($query)=>$query->where('id','!=',$material->id))->exists()){
   $candidate=$base.'-'.$suffix++;
  }
  return $candidate;
 }
 public function teacherQuizzes(){
  $this->teacher();
  QuizSchedule::archiveExpired();
  $user = auth()->user();
  $showArchive = request()->boolean('arsip');
  $classes = $user->role === 'admin' ? SchoolClass::orderBy('name')->get() : $user->teachingClasses()->orderBy('name')->get();
  $assessments = Assessment::query()
   ->whereIn('class_id', $classes->pluck('id'))
   ->where('author_id', $user->id)
   ->when($showArchive,
       fn ($query) => $query->whereNotNull('archived_at'),
       fn ($query) => $query->whereNull('archived_at'))
   ->withCount(['questions', 'attempts'])->with(['attempts' => fn ($q) => $q->with('user:id,name')->orderByDesc('submitted_at')])
   ->latest()->get();
  return view('pages.guru.classroom-quiz', compact('assessments', 'classes', 'showArchive'));
 }
 public function studentQuizzes(){
  QuizSchedule::archiveExpired();
  abort_unless(auth()->user()->role==='siswa',403);
  $classes=auth()->user()->classes()->orderBy('name')->get();
  $activeClassId=(int)session('kelas_aktif_id');
  $classIds=$activeClassId&&$classes->contains('id',$activeClassId)
   ?[$activeClassId]
   :$classes->pluck('id')->all();
  $assessments=Assessment::query()
   ->where('is_published',true)
   ->whereNull('archived_at')
   ->whereIn('class_id',$classIds)
   ->withCount('questions')
   ->with(['attempts'=>fn($query)=>$query
    ->where('user_id',auth()->id())
    ->orderByDesc('attempt_number')])
   ->latest()
   ->get();
  return view('pages.siswa.kuis',compact('assessments','classes','activeClassId'));
 }
 public function startQuiz(Request $r){
  $this->teacher();
  $v=$r->validate([
   'title'=>'required|string|max:255',
   'class_id'=>'required|integer',
   'type'=>'required|in:latihan,kuis,ujian',
   'time_limit_minutes'=>'required|integer|between:1,600',
   'starts_at'=>'nullable|date_format:Y-m-d\\TH:i',
   'due_at'=>'nullable|date_format:Y-m-d\\TH:i',
  ]);
  $schedule=QuizSchedule::validateDates($v);
  $class=$this->owned((int)$v['class_id']);
  $assessment=Assessment::create([
   'author_id'=>$r->user()->id,
   'class_id'=>$class->id,
   'title'=>$v['title'],
   'type'=>$v['type'],
   'time_limit_minutes'=>$v['time_limit_minutes'],
   'starts_at'=>$schedule['starts_at'],
   'due_at'=>$schedule['due_at'],
   'is_published'=>false,
   'max_attempts'=>2,
   'passing_score'=>70,
  ]);
  return redirect()->route('teacher.quizzes.edit',$assessment);
 }
 public function editQuiz(Assessment $assessment){
  $this->teacher();
  abort_if($assessment->archived_at !== null, 403, 'Pulihkan kuis dari Arsip sebelum diedit.');
  abort_unless($assessment->author_id===auth()->id() || auth()->user()->role==='admin',403);
  $user=auth()->user();
  $classes=$user->role==='admin'
   ? SchoolClass::orderBy('name')->get()
   : $user->teachingClasses()->orderBy('name')->get();
  $assessment->load('questions');
  $materials = Material::query()->whereIn('class_id', $classes->pluck('id'))
   ->where('is_published', true)->orderBy('title')->get(['id', 'title', 'class_id']);
  $materialOptions = $materials->map(fn ($item) => [
   'id' => $item->id, 'title' => $item->title, 'class_id' => $item->class_id,
  ])->values()->all();
  $locked = $assessment->attempts()->exists();
  return view('pages.shared.see-quiz', compact('assessment', 'classes', 'materials', 'materialOptions', 'locked'));
 }
 public function saveQuiz(Request $r,Assessment $assessment){
  $this->teacher();
  abort_if($assessment->archived_at !== null, 403, 'Pulihkan kuis dari Arsip sebelum diedit.');
  abort_unless($assessment->author_id===$r->user()->id || $r->user()->role==='admin',403);
  abort_if($assessment->attempts()->exists(),403,'Kuis yang sudah dikerjakan tidak dapat diubah.');
  $v=$r->validate([
   'title'=>'required|string|max:255',
   'class_id'=>'required|integer',
   'type'=>'required|in:latihan,kuis,ujian',
   'time_limit_minutes'=>'required|integer|between:1,600',
   'starts_at'=>'nullable|date_format:Y-m-d\\TH:i',
   'due_at'=>'nullable|date_format:Y-m-d\\TH:i',
   'passing_score'=>'required|integer|between:1,100',
   'max_attempts'=>'required|integer|between:1,5',
   'material_id'=>'nullable|integer',
   'questions'=>'required|array|min:1|max:50',
   'questions.*.id'=>'nullable|integer',
   'questions.*.text'=>'required|string|max:3000',
   'questions.*.question_type'=>'nullable|in:multiple_choice,short_answer',
   'questions.*.options'=>'nullable|array',
   'questions.*.options.*'=>'nullable|string|max:1000',
   'questions.*.correct_answer'=>'nullable|integer|between:0,3',
   'questions.*.short_answer'=>'nullable|string|max:1000',
   'questions.*.points'=>'required|integer|min:1|max:1000',
   'questions.*.explanation'=>'nullable|string|max:3000',
   'questions.*.material_id'=>'nullable|integer',
   'questions.*.resource_url'=>'nullable|url:http,https|max:2048',
   'questions.*.file'=>'nullable|file|mimes:jpg,jpeg,png,webp,pdf,doc,docx,ppt,pptx,mp4,webm|max:20480',
   'questions.*.remove_file'=>'nullable|boolean',
  ]);
  $schedule=QuizSchedule::validateDates($v);
  $class=$r->user()->role==='admin'
   ? SchoolClass::findOrFail($v['class_id'])
   : $this->owned((int)$v['class_id']);
  $original=$assessment->questions()->get()->keyBy('id');
  $existingIds=[];
  foreach($v['questions'] as $index=>$question){
   $oldId=(int)($question['id']??0);
   if($oldId){
    if(!$original->has($oldId) || in_array($oldId,$existingIds,true)){
     throw ValidationException::withMessages(['questions'=>'Soal tidak terdaftar di kuis ini.']);
    }
    $existingIds[]=$oldId;
   }
   $type=$question['question_type']??'multiple_choice';
   if($type==='multiple_choice'){
    $options=$question['options']??null;
    if(!is_array($options) || count($options)!==4 || array_keys($options)!==[0,1,2,3] ||
       count(array_filter($options,static fn($opt)=>!is_string($opt) || trim($opt)===''))){
     throw ValidationException::withMessages(['questions.'.$index.'.options'=>'Isi keempat pilihan jawaban.']);
    }
    if(!isset($question['correct_answer'])){
     throw ValidationException::withMessages(['questions.'.$index.'.correct_answer'=>'Pilih kunci jawaban.']);
    }
   } else {
    if(trim((string)($question['short_answer']??''))===''){
     throw ValidationException::withMessages(['questions.'.$index.'.short_answer'=>'Isi kunci jawaban isian singkat.']);
    }
   }
  }
  $materialIds = collect([$v['material_id'] ?? null])
   ->concat(collect($v['questions'])->pluck('material_id'))
   ->filter(fn ($id) => $id !== null && $id !== '')->map(fn ($id) => (int) $id)->unique();
  if ($materialIds->isNotEmpty()) {
   $allowed = Material::query()->where('class_id', $class->id)->where('is_published', true)
    ->whereIn('id', $materialIds)->count();
   if ($allowed !== $materialIds->count()) {
    throw ValidationException::withMessages(['material_id' => 'Materi remedial wajib terbit dan berada dalam kelas kuis.']);
   }
  }
  $uploaded=[];
  $obsolete=[];
  try {
   DB::transaction(function()use($assessment,$v,$r,$class,$schedule,$original,&$uploaded,&$obsolete){
    $assessment->update([
     'title'=>$v['title'],'class_id'=>$class->id,'type'=>$v['type'],
     'time_limit_minutes'=>$v['time_limit_minutes'],'passing_score'=>$v['passing_score'],
     'max_attempts'=>$v['max_attempts'],'starts_at'=>$schedule['starts_at'],
     'due_at'=>$schedule['due_at'],'material_id'=>$v['material_id'] ?? null,'is_published'=>true,
    ]);
    $retained=[];
    foreach($v['questions'] as $index=>$question){
     $type=$question['question_type']??'multiple_choice';
     $oldId=(int)($question['id']??0);
     $model=$oldId ? $original->get($oldId) : $assessment->questions()->make();
     $path=$model->attachment_path;
     $name=$model->attachment_name;
     if($r->hasFile('questions.'.$index.'.file')){
      $file=$r->file('questions.'.$index.'.file');
      $newPath=$file->store('mathleaf/quiz-attachments','local');
      $uploaded[]=$newPath;
      if($path)$obsolete[]=$path;
      $path=$newPath;
      $name=Str::limit($file->getClientOriginalName(),240,'');
     } elseif(!empty($question['remove_file'])){
      if($path)$obsolete[]=$path;
      $path=null;$name=null;
     }
     $model->fill([
      'question_text'=>$question['text'],
      'type'=>$type,
      'options'=>$type==='multiple_choice'?array_values($question['options']):null,
      'correct_answer'=>$type==='multiple_choice'?(string)$question['correct_answer']:trim($question['short_answer']),
      'points'=>$question['points'],
      'explanation'=>$question['explanation']??null,
      'material_id'=>$question['material_id']??null,
      'sort_order'=>$index,
      'attachment_path'=>$path,
      'attachment_name'=>$name,
      'resource_url'=>$question['resource_url']??null,
     ]);
     $assessment->questions()->save($model);
     $retained[]=$model->id;
    }
    foreach($original as $oldQuestion){
     if(!in_array($oldQuestion->id,$retained,true)){
      if($oldQuestion->attachment_path)$obsolete[]=$oldQuestion->attachment_path;
      $oldQuestion->delete();
     }
    }
   },3);
  } catch (\Throwable $error) {
   if($uploaded)Storage::disk('local')->delete($uploaded);
   throw $error;
  }
  if($obsolete){
   try { Storage::disk('local')->delete(array_values(array_unique($obsolete))); }
   catch (\Throwable $exception) { report($exception); }
  }
  return redirect()->route('teacher.quizzes')->with('success','Kuis berhasil disimpan dan dipublikasikan.');
 }
 public function quizAttempt(Request $request, AssessmentAttempt $attempt){
  $this->teacher();
  $attempt->load(['assessment', 'answers.question.material', 'user:id,name']);
  abort_unless($attempt->assessment && $attempt->assessment->class_id, 403);
  if (auth()->user()->role !== 'admin') {
   $this->owned((int) $attempt->assessment->class_id);
   abort_unless((int) $attempt->assessment->author_id === (int) auth()->id(), 403);
  }
  return view('pages.guru.quiz-attempt', compact('attempt'));
 }
 public function setActiveClass(Request $r){
  $v=$r->validate(['kelas_id'=>'nullable|integer']);
  $user=$r->user();
  $classId=$v['kelas_id']??null;
  if(empty($classId)){
   $r->session()->forget('kelas_aktif_id');
   return $this->redirectAfterClassChange($r,null);
  }
  $classes=in_array($user->role,['guru','admin'],true)?$user->teachingClasses():$user->classes();
  abort_unless($classes->whereKey($classId)->exists(),403);
  $r->session()->put('kelas_aktif_id',(int)$classId);
  return $this->redirectAfterClassChange($r,(int)$classId);
 }
 private function redirectAfterClassChange(Request $r,?int $classId){
  $previousPath=parse_url((string)$r->headers->get('referer'),PHP_URL_PATH)?:'';
  if(preg_match('~(?:^|/)guru/rekap-nilai(?:/siswa/(\d+))?/?$~',$previousPath,$matches)){
   if(!empty($matches[1]))return redirect()->route('teacher.grades.student',array_filter([
    'student'=>(int)$matches[1],
    'class_id'=>$classId,
   ],static fn($value)=>$value!==null));
   return redirect()->route('teacher.grades',$classId!==null?['class_id'=>$classId]:[]);
  }
  return back();
 }
 public function createClass(Request $r){
  $this->teacher();
  $v=$r->validate(['name'=>'required|string|max:100','school_year'=>'nullable|string|max:15','max_students'=>'nullable|integer|between:1,200']);
  do{$code=Str::upper(Str::random(8));}while(SchoolClass::where('join_code',$code)->exists());
  auth()->user()->teachingClasses()->create($v+['max_students'=>$v['max_students']??30,'join_code'=>$code]);
  return back()->with('success','Kelas berhasil dibuat dengan batas '.($v['max_students']??30).' siswa.');
 }

 public function updateCapacity(Request $request, SchoolClass $schoolClass){
  $this->teacher();
  abort_unless((int)$schoolClass->teacher_id === (int)$request->user()->id, 403);
  $v=$request->validate(['max_students'=>'required|integer|between:1,200']);
  DB::transaction(function () use ($request, $schoolClass, $v) {
   $class=SchoolClass::whereKey($schoolClass->id)->lockForUpdate()->firstOrFail();
   abort_unless((int)$class->teacher_id === (int)$request->user()->id, 403);
   $count=$class->students()->count();
   if ($v['max_students'] < $count) {
    throw ValidationException::withMessages(['max_students'=>'Kuota tidak boleh lebih kecil daripada jumlah siswa saat ini ('.$count.').']);
   }
   $class->update(['max_students'=>$v['max_students']]);
  }, 3);
  return back()->with('success','Kapasitas kelas diperbarui menjadi '.$v['max_students'].' siswa.');
 }

 public function allowStudent(Request $request, SchoolClass $schoolClass, \App\Models\User $student){
  $this->teacher();
  abort_unless((int)$schoolClass->teacher_id === (int)$request->user()->id, 403);
  DB::table('class_kicked_users')->where('class_id',$schoolClass->id)->where('user_id',$student->id)->delete();
  return back()->with('success','Izin bergabung '.$student->name.' sudah dibuka kembali.');
 }
 /** Hanya guru pemilik yang boleh mengeluarkan anggota; akun dan nilai siswa tetap tersimpan. */
 public function removeStudent(Request $request, SchoolClass $schoolClass, \App\Models\User $student){
  $this->teacher();
  abort_unless((int)$schoolClass->teacher_id === (int)$request->user()->id, 403);
  abort_unless($student->role === 'siswa', 404);
  abort_unless($schoolClass->students()->whereKey($student->id)->exists(), 404);
  DB::transaction(function () use ($schoolClass, $student) {
   $schoolClass->students()->detach($student->id);
   DB::table('class_kicked_users')->updateOrInsert(
    ['class_id'=>$schoolClass->id,'user_id'=>$student->id],
    ['created_at'=>now(),'updated_at'=>now()]
   );
  }, 3);
  return redirect()->route('classroom.home')->with('success', $student->name.' dikeluarkan. Dia tidak bisa masuk lagi sampai guru membuka izin.');
 }

 /** Hapus permanen kelas dan seluruh data belajar milik kelas tersebut, dengan konfirmasi nama kelas. */
 public function destroyClass(Request $request, SchoolClass $schoolClass){
  $this->teacher();
  abort_unless((int)$schoolClass->teacher_id === (int)$request->user()->id, 403);
  $input=$request->validate(['confirmation'=>'required|string|max:100']);
  if ($input['confirmation'] !== $schoolClass->name) {
   throw ValidationException::withMessages(['confirmation'=>'Nama kelas tidak sesuai. Penghapusan dibatalkan.']);
  }

  $classId=(int)$schoolClass->id;
  $className=$schoolClass->name;
  $filePaths=[];
  DB::transaction(function () use ($request, $classId, &$filePaths) {
   $class=SchoolClass::query()->whereKey($classId)->lockForUpdate()->firstOrFail();
   abort_unless((int)$class->teacher_id === (int)$request->user()->id, 403);

   // Ambil berkas privat sebelum relasi dihapus. Hapus fisik setelah transaksi sukses.
   $filePaths=array_merge(
    DB::table('materials')->where('class_id',$classId)->whereNotNull('file_path')->pluck('file_path')->all(),
    DB::table('submissions')->whereIn('activity_id',DB::table('activities')->select('id')->where('class_id',$classId))
     ->whereNotNull('file_path')->pluck('file_path')->all(),
    DB::table('activities')->where('class_id',$classId)->whereNotNull('attachment_path')->pluck('attachment_path')->all(),
    DB::table('questions')->whereIn('assessment_id',DB::table('assessments')->select('id')->where('class_id',$classId))
     ->whereNotNull('attachment_path')->pluck('attachment_path')->all()
   );

   // class_id pada materials dan assessments memakai nullOnDelete; hapus konten terkait
   // secara eksplisit agar materi, nilai, dan progres tidak tertinggal sebagai data yatim.
   // FK turunan (jawaban kuis, percobaan, komentar, pengumpulan) menghapus secara cascade.
   DB::table('assessments')->where('class_id',$classId)->delete();
   DB::table('forum_threads')->where('class_id',$classId)->delete();
   DB::table('activities')->where('class_id',$classId)->delete();
   DB::table('materials')->where('class_id',$classId)->delete();
   DB::table('study_groups')->where('class_id',$classId)->delete();
   $class->delete(); // class_user terhapus melalui FK cascade; akun user tetap ada.
  }, 3);

  $filePaths=array_values(array_unique(array_filter($filePaths,static fn($path)=>is_string($path) && $path!=='')));
  if ($filePaths) {
   try { Storage::disk('local')->delete($filePaths); }
   catch (\Throwable $exception) { report($exception); }
  }
  if ((int)$request->session()->get('kelas_aktif_id') === $classId) {
   $request->session()->forget('kelas_aktif_id');
  }
  return redirect()->route('classroom.home')->with('success','Kelas '.$className.' dan seluruh data pembelajarannya berhasil dihapus.');
 }

 // Alur satu kelas: bergabung hanya dilakukan saat login siswa dengan kode kelas.
 public function joinClass(Request $r){
  abort_unless($r->user()->role==='siswa',403);
  throw ValidationException::withMessages(['join_code'=>'Untuk bergabung ke kelas Matematika, keluar lalu login kembali dengan kode kelas dari guru.']);
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
 public function takeQuiz(Assessment $assessment){
  $user=auth()->user();
  abort_unless($user->role==='siswa'&&$assessment->is_published&&$assessment->class_id&&$this->enrolled($assessment->class_id),403);
  $assessment->load('questions');
  $already=$user->attempts()->where('assessment_id',$assessment->id)->count();
  return view('pages.shared.see-quiz',compact('assessment','already'));
 }
 public function submitQuiz(Request $r,Assessment $assessment){
  abort_unless($r->user()->role==='siswa'&&$assessment->is_published&&$assessment->class_id&&$this->enrolled($assessment->class_id),403);
  $questions=$assessment->questions()->get();
  abort_if($questions->isEmpty(),403,'Kuis belum memiliki soal.');
  $answerRules=['answers'=>'required|array|size:'.$questions->count()];
  foreach($questions as $question){
   $answerRules['answers.'.$question->id]='required|integer|between:0,'.(max(0,count($question->options??[])-1));
  }
  $validated=$r->validate($answerRules);
  $already=$r->user()->attempts()->where('assessment_id',$assessment->id)->count();
  abort_if($already >= $assessment->max_attempts,403,'Percobaan sudah habis.');
  $total=$questions->sum('points');
  abort_if($total<=0,422,'Total poin kuis tidak valid.');
  $correct=0;
  $savedAttempt=DB::transaction(function()use($r,$assessment,$questions,$already,$total,&$correct,$validated){
   $savedAttempt=$assessment->attempts()->create([
    'user_id'=>$r->user()->id,
    'attempt_number'=>$already+1,
    'status'=>'graded',
    'submitted_at'=>now(),
    'score'=>0,
   ]);
   $points=0;
   foreach($questions as $question){
    $answer=(string)$validated['answers'][$question->id];
    $isCorrect=$answer===(string)$question->correct_answer;
    if($isCorrect){$correct++;$points+=$question->points;}
    $savedAttempt->answers()->create([
     'question_id'=>$question->id,
     'answer_text'=>$answer,
     'is_correct'=>$isCorrect,
     'points_awarded'=>$isCorrect?$question->points:0,
     'feedback'=>$question->explanation,
    ]);
   }
   $savedAttempt->update(['score'=>round($points/$total*100,2)]);
   return $savedAttempt;
  });
  return redirect()->route('siswa.kuis')->with('success','Kuis terkirim! Nilai: '.$savedAttempt->score.' / 100 ('.$correct.' jawaban benar).');
 }
}
