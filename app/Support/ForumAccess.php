<?php

namespace App\Support;

use App\Models\SchoolClass;
use App\Models\User;

class ForumAccess
{
    public static function classIds(User $user): array
    {
        if ($user->role === 'admin') {
            return SchoolClass::query()->pluck('id')->map(fn ($id) => (int) $id)->all();
        }

        if ($user->role === 'guru') {
            return $user->teachingClasses()->pluck('id')->map(fn ($id) => (int) $id)->all();
        }

        return $user->classes()->pluck('classes.id')->map(fn ($id) => (int) $id)->all();
    }

    public static function canAccess(User $user, ?int $classId): bool
    {
        return $classId !== null && in_array($classId, self::classIds($user), true);
    }
}
