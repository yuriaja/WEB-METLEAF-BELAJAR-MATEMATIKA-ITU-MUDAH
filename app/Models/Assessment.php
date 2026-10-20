<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Assessment extends Model {
    protected $table = 'assessments';
    protected $guarded = ['id'];
    protected function casts(): array { return ['options'=>'array', 'is_published'=>'boolean', 'is_pinned'=>'boolean', 'completed_at'=>'datetime', 'submitted_at'=>'datetime', 'starts_at'=>'datetime', 'due_at'=>'datetime']; }
    public function material() { return $this->belongsTo(Material::class, 'material_id'); }
public function questions() { return $this->hasMany(Question::class)->orderBy('sort_order'); }
public function attempts() { return $this->hasMany(AssessmentAttempt::class); }
}
