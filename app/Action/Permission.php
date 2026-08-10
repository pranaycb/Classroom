<?php

namespace App\Action;

use Illuminate\Support\Facades\Auth;

class Permission
{
    /**
     * Get user permissions
     */
    public static function get($user = null): array|string
    {
        $classroom = request()->route('classroom');

        $role = $classroom?->role($user ?? Auth::id());

        return match ($role) {
            'teacher' => 'all',
            'moderator' => $classroom->moderator_permissions,
            default => [],
        };
    }

    /**
     * Check if user has permission
     */
    public static function has(string|array $key, $user = null): bool
    {
        $permissions = self::get($user);

        /**
         * Check if user has all permissions
         */
        if ($permissions === 'all') return true;

        $keys = is_array($key) ? $key : [$key];

        /**
         * Check if user has any permission
         */
        $result = array_map(fn($k) => data_get($permissions, $k, false), $keys);

        return is_array($result) ? in_array(true, $result) : $result;
    }

    /**
     * Check if teacher
     */
    public static function isTeacher(): bool
    {
        $classroom = request()->route('classroom');
        return $classroom->role(Auth::id()) === 'teacher';
    }

    /**
     * Check if moderator
     */
    public static function isModerator(): bool
    {
        $classroom = request()->route('classroom');
        return $classroom->role(Auth::id()) === 'moderator';
    }

    /**
     * Check if student
     */
    public static function isStudent(): bool
    {
        $classroom = request()->route('classroom');
        return $classroom->role(Auth::id()) === 'student';
    }
}
