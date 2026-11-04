<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumComment extends Model
{
    protected $table = 'forum_comments';
    protected $guarded = ['id'];

    public function author() { return $this->belongsTo(User::class, 'user_id'); }
    public function forumThread() { return $this->belongsTo(ForumThread::class, 'forum_thread_id'); }
}
