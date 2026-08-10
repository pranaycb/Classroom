<?php

namespace App\Http\Controllers\Dashboard;

use Inertia\Inertia;
use App\Models\Classroom;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ClassResource;

class HomeController extends Controller
{
    public function __invoke($slug = 'all')
    {
        $user = Auth::id();

        $rooms = Classroom::query()
            ->where(function($query) use($slug, $user) {
                return match($slug) {
                    'created' => $query->where('user_id', $user),
                    'joined' => $query->where('user_id', '!=', $user)
                        ->withWhereHas('students', function ($q) use ($user) {
                            $q->where('user_id', $user)
                                ->where('status', 'approved');
                        }),
                    default => $query
                };
            })
            ->orderBy('id', 'desc')
            ->paginate(9)
            ->onEachSide(0)
            ->withQueryString();

        $data = [
            'active' => $slug ?? 'all',
            'classes' => Inertia::defer(fn() => new ClassResource($rooms)),
        ];

        return Inertia::render('Dashboard/Home', $data);
    }
}
