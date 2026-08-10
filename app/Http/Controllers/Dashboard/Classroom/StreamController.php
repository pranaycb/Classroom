<?php

namespace App\Http\Controllers\Dashboard\Classroom;

use Inertia\Inertia;
use App\Models\Classroom;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Number;
use Illuminate\Support\Facades\Auth;
use App\Http\Services\UploaderService;
use App\Http\Resources\AnnouncementResource;

class StreamController extends ClassroomController
{

    /**
     * Show all announcements
     */
    public function index(Classroom $classroom)
    {
        $announcements = $classroom->announcements()
            ->orderBy('id', 'desc')
            ->paginate(10, ['*'], 'stream')
            ->onEachSide(0)
            ->withQueryString();

        $data = [
            'announcements' => Inertia::defer(fn() => new AnnouncementResource($announcements)),
        ];

        return $this->render('Dashboard/Classroom/Stream/Index', $data);
    }

    /**
     * Handle an incoming announcement request
     */
    public function store(Classroom $classroom, Request $request, UploaderService $uploader)
    {
        abort_if(Auth::user()->cannot('create', Announcement::class), 401);

        $request->validate([
            'content' => 'required|doesnt_start_with:<p><br></p>',
            'attachments' => 'nullable|array|max:10',
            'attachments.*' => [
                'mimes:' . config('app.upload.mimetypes'),
                'max:' . config('app.upload.max'),
            ],
        ], [
            'content.required' => 'Write something to announce',
            'content.doesnt_start_with' => 'Write something to announce',
            'attachments.*.mimes' => 'File format not supported',
            'attachments.*.max' => 'Max upload size is 25mb',
        ]);

        $announcement = $classroom->announcements()->create([
            'user_id' => $request->user()->id,
            'content' => $request->content,
        ]);

        /**
         * If has attachments then upload it
         */
        if ($request->hasFile('attachments')) {

            foreach ($request->file('attachments') as $attachment) {

                $file = $uploader->upload($attachment, $classroom->code, 'attachments');

                $announcement->attachments()->create([
                    'name' => $attachment->getClientOriginalName(),
                    'file' => $file,
                    'type' => $attachment->extension(),
                    'size' => $attachment->getSize(),
                ]);
            }
        }

        return redirect()->back()->with('success', 'Announcement posted successfully');
    }

    /**
     * Edit an announcement
     */
    public function edit(Classroom $classroom, Announcement $announcement)
    {
        abort_if(Auth::user()->cannot('update', $announcement), 401);

        $data = [
            'data' => [
                'id' => $announcement->id,
                'content' => $announcement->content,
                'attachments' => $announcement->attachments->map(fn($attachment) => [
                    'id' => $attachment->id,
                    'name' => $attachment->name,
                    'icon' => $attachment->icon,
                    'size' => Number::fileSize($attachment->size),
                ])
            ],
        ];

        return $this->render('Dashboard/Classroom/Stream/Edit', $data);
    }

    /**
     * Update an announcement
     */
    public function update(Request $request, UploaderService $uploader, Classroom $classroom, Announcement $announcement)
    {
        abort_if(Auth::user()->cannot('update', $announcement), 401);

        $request->validate([
            'content' => 'required|doesnt_start_with:<p><br></p>',
            'attachments' => 'nullable|array|max:10',
            'attachments.*.file' => [
                'nullable',
                'required_without:attachments.*.id',
                'mimes:' . config('app.upload.mimetypes'),
                'max:' . config('app.upload.max'),
            ],
        ], [
            'content.required' => 'Write something to announce',
            'content.doesnt_start_with' => 'Write something to announce',
            'attachments.*.mimes' => 'File format not supported',
            'attachments.*.max' => 'Max upload size is 25mb',
        ]);

        $announcement->update([
            'content' => $request->content,
        ]);

        $attachmentIds = collect($request->attachments)->pluck('id')->filter()->toArray();

        // Delete removed attachments
        $announcement->attachments()->whereNotIn('id', $attachmentIds)->delete();

        /**
         * If has attachments then upload it
         */
        if ($request->filled('attachments')) {

            foreach ($request->attachments as $attachment) {

                if (empty($attachment['id']) && !empty($attachment['file'])) {

                    $file = $uploader->upload($attachment['file'], $classroom->code, 'attachments');

                    $announcement->attachments()->create([
                        'name' => $attachment['file']->getClientOriginalName(),
                        'file' => $file,
                        'type' => $attachment['file']->extension(),
                        'size' => $attachment['file']->getSize(),
                    ]);
                }
            }
        }

        return redirect()->route('dashboard.classroom.streams.index', $classroom->code)
            ->with('success', 'Announcement updated successfully');
    }

    /**
     * Delete announcement
     */
    public function destroy(Classroom $classroom, Announcement $announcement)
    {
        abort_if(Auth::user()->cannot('delete', $announcement), 401);

        $announcement->delete();
        $announcement->attachments()->delete();

        return redirect()->back()->with('success', 'Announcement deleted successfully');
    }
}
