<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Question extends Model {
    protected $table = 'questions';
    protected $guarded = ['id'];
    protected function casts(): array { return ['options'=>'array', 'is_published'=>'boolean', 'is_pinned'=>'boolean', 'completed_at'=>'datetime', 'submitted_at'=>'datetime', 'starts_at'=>'datetime', 'due_at'=>'datetime']; }
    public function assessment() { return $this->belongsTo(Assessment::class, 'assessment_id'); }

}
