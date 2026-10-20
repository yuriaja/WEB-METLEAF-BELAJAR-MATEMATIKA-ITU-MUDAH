<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ForumComment extends Model {
    protected $table = 'forum_comments';
    protected $guarded = ['id'];
    protected function casts(): array { return ['options'=>'array', 'is_published'=>'boolean', 'is_pinned'=>'boolean', 'completed_at'=>'datetime', 'submitted_at'=>'datetime', 'starts_at'=>'datetime', 'due_at'=>'datetime']; }
    public function author() { return $this->belongsTo(User::class, 'user_id'); }

}
