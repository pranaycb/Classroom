<?php

namespace App\Http\Controllers\Dashboard\Classroom;

use Inertia\Inertia;
use App\Models\Classroom;
use App\Action\Permission;
use App\Models\Attachment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ClassroomController extends Controller
{
    /**
     * Get room data
     */
    protected function room(Classroom $classroom)
    {
        $data = [
            'role' => $classroom->role(Auth::id()),
            'title' => $classroom->title,
            'teacher' => $classroom->teacher->name,
            'section' => $classroom->section,
            'room' => $classroom->room,
            'theme' => $classroom->theme,
            'code' => $classroom->code,
            'permissions' => Permission::get(),
        ];

        return $data;
    }

    /**
     * Handle an incoming download request
     */
    public function download(Classroom $classroom, Attachment $attachment)
    {
        $file = $attachment->file;

        if (Storage::disk('public')->exists($file)) {
            $path = Storage::disk('public')->path($file);
            return response()->download($path, $attachment->name);
        }

        abort(404);
    }

    /**
     * Render an vue file
     */
    public function render($path, $data = [])
    {
        $room = $this->room(request()->route('classroom'));

        $data = array_merge($data, [
            'room' => $room
        ]);

        return Inertia::render($path, $data);
    }
}
