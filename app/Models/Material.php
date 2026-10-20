<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Material extends Model {
    protected $table = 'materials';
    protected $guarded = ['id'];
    protected function casts(): array { return ['options'=>'array', 'is_published'=>'boolean', 'is_pinned'=>'boolean', 'completed_at'=>'datetime', 'submitted_at'=>'datetime', 'starts_at'=>'datetime', 'due_at'=>'datetime']; }
    public function author() { return $this->belongsTo(User::class, 'author_id'); }
public function progress() { return $this->hasMany(MaterialProgress::class); }
public function assessments() { return $this->hasMany(Assessment::class); }
}
