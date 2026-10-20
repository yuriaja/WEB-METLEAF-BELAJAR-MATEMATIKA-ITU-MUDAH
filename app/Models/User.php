<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'student_number'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    public function classes() { return $this->belongsToMany(SchoolClass::class, 'class_user', 'user_id','class_id')->withTimestamps(); }
    public function teachingClasses() { return $this->hasMany(SchoolClass::class, 'teacher_id'); }
    public function attempts() { return $this->hasMany(AssessmentAttempt::class); }
    public function submissions() { return $this->hasMany(Submission::class); }
    public function progress() { return $this->hasMany(MaterialProgress::class); }
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
