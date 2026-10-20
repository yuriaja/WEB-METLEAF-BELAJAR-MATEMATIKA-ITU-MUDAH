<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Submission extends Model {
    protected $table = 'submissions';
    protected $guarded = ['id'];
    protected function casts(): array { return ['options'=>'array', 'is_published'=>'boolean', 'is_pinned'=>'boolean', 'completed_at'=>'datetime', 'submitted_at'=>'datetime', 'starts_at'=>'datetime', 'due_at'=>'datetime']; }
    public function activity() { return $this->belongsTo(Activity::class, 'activity_id'); }
public function user() { return $this->belongsTo(User::class); }
public function group() { return $this->belongsTo(StudyGroup::class,'study_group_id'); }
}
