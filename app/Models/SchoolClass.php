<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SchoolClass extends Model {
    protected $table = 'classes';
    protected $guarded = ['id'];
    protected function casts(): array { return ['options'=>'array', 'is_published'=>'boolean', 'is_pinned'=>'boolean', 'completed_at'=>'datetime', 'submitted_at'=>'datetime', 'starts_at'=>'datetime', 'due_at'=>'datetime']; }
    public function teacher() { return $this->belongsTo(User::class, 'teacher_id'); }
public function students() { return $this->belongsToMany(User::class, 'class_user', 'class_id','user_id')->withTimestamps(); }
public function activities() { return $this->hasMany(Activity::class,'class_id'); }
public function groups() { return $this->hasMany(StudyGroup::class,'class_id'); }
}
