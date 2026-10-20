<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class MaterialProgress extends Model {
    protected $table = 'material_progress';
    protected $guarded = ['id'];
    protected function casts(): array { return ['options'=>'array', 'is_published'=>'boolean', 'is_pinned'=>'boolean', 'completed_at'=>'datetime', 'submitted_at'=>'datetime', 'starts_at'=>'datetime', 'due_at'=>'datetime']; }
    public function material() { return $this->belongsTo(Material::class, 'material_id'); }

}
