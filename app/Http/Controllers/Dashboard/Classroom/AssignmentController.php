<?php

namespace App\Http\Controllers\Dashboard\Classroom;

use Firebase\JWT\JWT;
use Pest\Support\Str;
use App\Models\Classroom;
use App\Action\Permission;
use App\Models\Assignment;
use App\Models\OnlineClass;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Number;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use App\Http\Services\UploaderService;
use App\Http\Resources\CommentResource;
use App\Http\Resources\AssignmentResource;
use App\Http\Resources\AttachmentResource;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class AssignmentController extends ClassroomController implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('hasaccess:assignment.create', only: ['create', 'store']),
            new Middleware('hasaccess:assignment.update', only: ['edit', 'update']),
            new Middleware('hasaccess:assignment.delete', only: ['delete']),
        ];
    }

    /**
     * Show all classes
     */
    public function index(Classroom $classroom)
    {
        $assignments = $classroom->assignments()
            ->whereDate('due', '>=', now())
            ->orderBy('due', 'asc')
            ->paginate(10)
            ->onEachSide(0)
            ->withQueryString();

        $data = [
            'assignments' => new AssignmentResource($assignments),
        ];

        return $this->render('Dashboard/Classroom/Assignment/Index', $data);
    }

    /**
     * Create a class
     */
    public function create(Classroom $classroom)
    {
        return $this->render('Dashboard/Classroom/Assignment/Create');
    }

    /**
     * Store a created class record
     */
    public function store(Request $request, UploaderService $uploader, Classroom $classroom)
    {
        $request->validate([
            'title' => 'required',
            'due' => 'required|date',
            'marks' => 'required|numeric',
            'description' => 'required',
            'attachments' => 'nullable|array|max:10',
            'attachments.*' => [
                'mimes:' . config('app.upload.mimetypes'),
                'max:' . config('app.upload.max'),
            ],
        ], [
            'attachments.*.mimes' => 'File format not supported',
            'attachments.*.max' => 'Max upload size is 25mb',
        ]);

        $assignment = $classroom->assignments()->create([
            'title' => $request->title,
            'due' => $request->due,
            'marks' => $request->marks,
            'description' => $request->description,
        ]);

        /**
         * If has attachments then upload it
         */
        if ($request->hasFile('attachments')) {

            foreach ($request->file('attachments') as $attachment) {

                $file = $uploader->upload($attachment, $classroom->code, 'attachments');

                $assignment->attachments()->create([
                    'name' => $attachment->getClientOriginalName(),
                    'file' => $file,
                    'type' => $attachment->extension(),
                    'size' => $attachment->getSize(),
                ]);
            }
        }

        return redirect()->route('dashboard.classroom.assignments.show', [$classroom->code, $assignment->id])
            ->with('success', 'Assignment created successfully');
    }

    /**
     * Show a specific class
     */
    public function show(Classroom $classroom, Assignment $assignment)
    {
        $comments = $assignment->comments()
            ->orderBy('id', 'desc')
            ->paginate(10, ['*'], 'comment')
            ->onEachSide(0)
            ->withQueryString();

        $data = [
            'data' => [
                'id' => $assignment->id,
                'title' => $assignment->title,
                'created' => $assignment->created_at->format('M d, Y \a\t h:i a'),
                'due' => $assignment->due->format('M d, Y \a\t h:i a'),
                'marks' => round($assignment->marks, 2),
                'description' => $assignment->description,
                'attachments' => AttachmentResource::make($assignment->attachments),
            ],
            'comments' => new CommentResource($comments),
        ];

        if(Permission::isStudent()) {

            $submission = $assignment->submissions()->where('user_id', Auth::id())->first();

            $data['submission'] = [
                'status' => $submission->status ?? 'missing',
                'canSubmit' => is_null($submission) && now()->lt($assignment->due),
                'data' => empty($submission) ? null : [
                    'marks' => $submission->marks ? round($submission->marks, 2) : null,
                    'percentage' => $submission->marks ? $submission->percentage : null,
                    'feedback' => $submission->feedback,
                    'attachment' => [
                        'name' => $submission->attachment->name,
                        'icon' => $submission->attachment->icon,
                        'size' => Number::fileSize($submission->attachment->size),
                        'url' => route('dashboard.classroom.download', [
                            $classroom->code,
                            $submission->attachment->id,
                        ]),
                        'submitted' => $submission->attachment->created_at->format('M d, Y \a\t h:i a'),
                    ],
                ],
            ];
        }

        return $this->render('Dashboard/Classroom/Assignment/Details', $data);
    }

    /**
     * Edit a class
     */
    public function edit(Classroom $classroom, Assignment $assignment)
    {
        $data = [
            'data' => [
                'id' => $assignment->id,
                'title' => $assignment->title,
                'due' => $assignment->due->format('Y-m-d H:i'),
                'marks' => round($assignment->marks, 2),
                'description' => $assignment->description,
                'attachments' => $assignment->attachments->map(fn($attachment) => [
                    'id' => $attachment->id,
                    'name' => $attachment->name,
                    'icon' => $attachment->icon,
                    'size' => Number::fileSize($attachment->size),
                ])
            ],
        ];

        return $this->render('Dashboard/Classroom/Assignment/Edit', $data);
    }

    /**
     * Update a class
     */
    public function update(Request $request, UploaderService $uploader, Classroom $classroom, Assignment $assignment)
    {
        $request->validate([
            'title' => 'required',
            'due' => 'required|date',
            'marks' => 'required|numeric',
            'description' => 'required',
            'attachments' => 'nullable|array|max:10',
            'attachments.*.file' => [
                'nullable',
                'required_without:attachments.*.id',
                'mimes:' . config('app.upload.mimetypes'),
                'max:' . config('app.upload.max'),
            ],
        ], [
            'attachments.*.file.mimes' => 'File format not supported',
            'attachments.*.file.max' => 'Max upload size is 25mb',
        ]);

        $assignment->update([
            'title' => $request->title,
            'due' => $request->due,
            'marks' => $request->marks,
            'description' => $request->description,
        ]);

        $attachmentIds = collect($request->attachments)->pluck('id')->filter()->toArray();

        // Delete removed attachments
        $assignment->attachments()->whereNotIn('id', $attachmentIds)->delete();

        /**
         * If has attachments then upload it
         */
        if ($request->filled('attachments')) {

            foreach ($request->attachments as $attachment) {

                if(empty($attachment['id']) && !empty($attachment['file'])) {

                    $file = $uploader->upload($attachment['file'], $classroom->code, 'attachments');

                    $assignment->attachments()->create([
                        'name' => $attachment['file']->getClientOriginalName(),
                        'file' => $file,
                        'type' => $attachment['file']->extension(),
                        'size' => $attachment['file']->getSize(),
                    ]);
                }
            }
        }

        return redirect()->route('dashboard.classroom.assignments.show', [$classroom->code, $assignment->id])
            ->with('success', 'Assignment updated successfully');
    }

    /**
     * Delete a class
     */
    public function destroy(Classroom $classroom, Assignment $assignment)
    {
        $assignment->delete();
        $assignment->attachments()->delete();

        return redirect()->back()->with('success', 'Assignment deleted successfully');
    }
}
