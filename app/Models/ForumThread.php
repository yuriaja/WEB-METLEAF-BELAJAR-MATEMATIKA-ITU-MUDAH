<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumThread extends Model
{
    protected $table = 'forum_threads';
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['is_pinned' => 'boolean', 'is_locked' => 'boolean'];
    }

    public function schoolClass() { return $this->belongsTo(SchoolClass::class, 'class_id'); }
    public function author() { return $this->belongsTo(User::class, 'user_id'); }
    public function comments() { return $this->hasMany(ForumComment::class); }
}
