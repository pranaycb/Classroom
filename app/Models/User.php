<?php

namespace App\Models;

use App\Models\Classroom;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'password',
        'profile',
        'metric_id',
        'university',
        'department',
        'designation',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the profile photo path.
     */
    public function getProfilePathAttribute()
    {
        if (!is_null($this->profile) && Storage::disk('public')->exists('users/' . $this->profile)) {
            return asset('storage/users/' . $this->profile);
        }

        return asset('storage/icons/user.png');
    }
}
