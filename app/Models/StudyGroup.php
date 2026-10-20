<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class StudyGroup extends Model {
    protected $table = 'study_groups';
    protected $guarded = ['id'];
    protected function casts(): array { return ['options'=>'array', 'is_published'=>'boolean', 'is_pinned'=>'boolean', 'completed_at'=>'datetime', 'submitted_at'=>'datetime', 'starts_at'=>'datetime', 'due_at'=>'datetime']; }
    public function schoolClass() { return $this->belongsTo(SchoolClass::class, 'class_id'); }
public function members() { return $this->belongsToMany(User::class,'study_group_user','study_group_id','user_id'); }
}
