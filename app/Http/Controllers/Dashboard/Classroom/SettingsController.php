<?php

namespace App\Http\Controllers\Dashboard\Classroom;

use App\Action\Permission;
use Inertia\Inertia;
use App\Models\Classroom;
use App\Http\Controllers\Dashboard\Classroom\ClassroomController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends ClassroomController
{
    public function __construct()
    {
        abort_if(!Permission::isTeacher(), 401);
    }

    /**
     * Show settings page
     */
    public function index(Classroom $classroom)
    {
        $data = [
            'room' => [
                ...$this->room($classroom),
                'subject' => $classroom->subject,
                'moderation' => (bool) $classroom->moderation,
            ],
            'themes' => Storage::json('colors.json'),
        ];

        return Inertia::render('Dashboard/Classroom/Settings', $data);
    }

    /**
     * Update settings data
     */
    public function update(Request $request, Classroom $classroom)
    {
        $data = $request->validate([
            'title' => 'required|string|min:10|max:100',
            'section' => 'required',
            'subject' => 'required',
            'room' => 'required',
            'theme' => 'required',
            'moderation' => 'required|boolean'
        ]);

        $classroom->update($data);

        return redirect()->back()->with('success', 'Settings updated successfully');
    }
}
