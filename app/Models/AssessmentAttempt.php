<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class AssessmentAttempt extends Model {
    protected $table = 'assessment_attempts';
    protected $guarded = ['id'];
    protected function casts(): array { return ['options'=>'array', 'is_published'=>'boolean', 'is_pinned'=>'boolean', 'completed_at'=>'datetime', 'submitted_at'=>'datetime', 'starts_at'=>'datetime', 'due_at'=>'datetime']; }
    public function assessment() { return $this->belongsTo(Assessment::class, 'assessment_id'); }
public function answers() { return $this->hasMany(AssessmentAnswer::class); }
public function user() { return $this->belongsTo(User::class); }
}
