<?php

namespace App\Models;

use App\Observers\ClassroomObserver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy(ClassroomObserver::class)]
class Classroom extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'code',
        'title',
        'section',
        'subject',
        'room',
        'theme',
        'moderation',
        'student_permissions',
        'moderator_permissions',
        'status',
    ];

    protected $casts = [
        'student_permissions' => 'array',
        'moderator_permissions' => 'array',
    ];


    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        // Add scope for user specific data
        static::addGlobalScope('user', function (Builder $builder) {
            if(request()->route()->named('dashboard.*')) {
                $builder->where('user_id', Auth::id())
                    ->orWhereHas('participants', function ($q) {
                        $q->where(function($query) {
                            $query->where('user_id', Auth::id())->where('status', 'approved');
                        });
                    });
            }
        });
    }

    /**
     * Get teahcer of a classroom
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get participants of a classroom
     */
    public function participants()
    {
        return $this->belongsToMany(User::class, 'classroom_user', 'classroom_id', 'user_id')
            ->withPivot(['moderator', 'status'])
            ->withTimestamps();
    }

    /**
     * Get students of a classroom
     */
    public function students()
    {
       return $this->participants()->wherePivot('moderator', false);
    }

    /**
     * Get moderators of a classroom
     */
    public function moderators()
    {
        return $this->participants()->wherePivot('moderator', true);
    }

    /**
     * Latest assignment
     */
    public function assigned()
    {
        return $this->hasOne(Assignment::class)
            ->whereDate('due', '>=', now())
            ->whereDoesntHave('submissions', fn($q) => $q->where('user_id', Auth::id()))
            ->latest();
    }

    /**
     * Get announcements of a classroom
     */
    public function announcements()
    {
        return $this->hasMany(Announcement::class);
    }

    /**
     * Get classes of a classroom
     */
    public function classes()
    {
        return $this->hasMany(OnlineClass::class);
    }

    /**
     * Get exams of a classroom
     */
    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    /**
     * Get assignments of a classroom
     */
    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    /**
     * Get the role of the specific user for the classroom
     */
    public function role($userId = null)
    {
        $userId = $userId ?? Auth::id();

        // if the user is the classroom teacher
        if ($this->user_id === $userId) {
            return 'teacher';
        }

        // if the user is a student in this classroom
        $student = $this->participants()
            ->where('user_id', $userId)
            ->first();

        // if moderator
        if ($student->pivot->moderator) {
            return 'moderator';
        }

        return 'student';
    }
}
