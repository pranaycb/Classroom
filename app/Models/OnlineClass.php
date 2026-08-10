<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;

class OnlineClass extends Model
{
    protected $fillable = [
        'classroom_id',
        'roomid',
        'name',
        'start',
        'end',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'start' => 'datetime',
            'end' => 'datetime',
        ];
    }

    /**
     * Get classroom for the class
     */
    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    /**
     * Get status of the class
     */
    public function getStatusAttribute(): string
    {
        $now = now();

        if ($now->lessThan($this->start)) {
            return 'upcoming';
        }

        if ($now->greaterThan($this->start) && is_null($this->end)) {
            return 'ongoing';
        }

        if($now->between($this->start, $this->end)) {
            return 'ongoing';
        }

        return 'ended';
    }

    /**
     * Get duration of the class
     */
    public function getDurationAttribute()
    {
        $start = Carbon::parse($this->start);
        $end = Carbon::parse($this->end);

        return $start->diffForHumans($end, [
            'short' => false,
            'parts' => 2,
            'syntax' => Carbon::DIFF_ABSOLUTE,
        ]);
    }
}
