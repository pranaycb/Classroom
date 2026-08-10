<?php

namespace App\Http\Controllers\Dashboard;

use Inertia\Inertia;
use App\Models\Classroom;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Notifications\StudentJoinRequestNotification;

class GetStartController extends Controller
{
    /**
     * Display create / join form
     */
    public function index()
    {
        return Inertia::render('Dashboard/GetStart');
    }

    /**
     * Handle an incoming create request
     */
    public function create(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|min:10|max:100',
            'section' => 'required',
            'subject' => 'required',
            'room' => 'required',
            'moderation' => 'required|boolean'
        ]);

        $room = Classroom::create([
            'user_id' => $request->user()->id,
            'code' => Str::lower(Str::random(11)),
            ...$validated,
        ]);

        /**
         * Create a directory for the classroom
         */
        Storage::disk('public')->makeDirectory('rooms/' . $room->code . '');

        return redirect()->route('dashboard.classroom.streams.index', $room->code)
            ->with('success', 'Classroom created successfully');
    }

    /**
     * Handle an incoming join request
     */
    public function join(Request $request)
    {
        $request->validate([
            'code' => 'required|exists:classrooms,code',
        ]);

        $classroom = Classroom::query()
            ->withoutGlobalScope('user')
            ->whereCode($request->code)
            ->with(['teacher'])
            ->with('students', function ($query) use ($request) {
                return $query->where('user_id', $request->user()->id);
            })
            ->first();

        /**
         * Check if student already joined in this room
         */
        if ($classroom?->students->isNotEmpty()) {

            $student = $classroom->students->first();

            $message = match ($student->pivot->status) {
                'rejected' => 'You have already joined this classroom',
                'pending' => 'You have already requested to join this room. Please wait for your request to be approved.',
                'banned' => 'You cant join this room.',
            };

            return redirect()->back()->with('status', $message);
        }

        $status = $classroom->moderation ? 'pending' : 'approved';

        $classroom->students()->attach($request->user()->id, [
            'status' => $status,
        ]);

        if ($status === 'pending') {

            /**
             * Notify teacher if pending
             */
            $classroom->teacher->notify(new StudentJoinRequestNotification($request->user(), $classroom));

            return redirect()->back()->with('status', 'Your request to join "' . $classroom->title . '" has been sent to the teacher and is currently awaiting approval. We will notify you by email once your request has been approved. Thanks for your patience!');
        }

        return redirect()->route('dashboard.classroom.streams.index', $classroom->code)
            ->with('success', 'Joined successfully');
    }

    /**
     * Handle an incoming join request
     */
    public function joinViaLink(Request $request, string $code)
    {
        $classroom = Classroom::query()
            ->withoutGlobalScope('user')
            ->whereCode($code)
            ->with(['teacher'])
            ->with('students', function ($query) use ($request) {
                return $query->where('user_id', $request->user()->id);
            })
            ->firstOrFail();

        /**
         * Check if student already joined in this room
         */
        if ($classroom->students->isNotEmpty()) {

            $student = $classroom->students->first();

            $message = match ($student->pivot->status) {
                'rejected' => 'You have already joined this classroom',
                'pending' => 'You have already requested to join this room. Please wait for your request to be approved.',
                'banned' => 'You cant join this room.',
                default => 'You have already joined this classroom',
            };

            return Inertia::render('Dashboard/JoinStatus', [
                'status' => $message
            ]);
        }

        $status = $classroom->moderation ? 'pending' : 'approved';

        $classroom->students()->attach($request->user()->id, [
            'status' => $status,
        ]);

        if ($status === 'pending') {

            /**
             * Notify teacher if pending
             */
            $classroom->teacher->notify(new StudentJoinRequestNotification($request->user(), $classroom));

            return Inertia::render('Dashboard/JoinStatus', [
                'status' => 'Your request to join "' . $classroom->title . '" has been sent to the teacher and is currently awaiting approval. We will notify you by email once your request has been approved. Thanks for your patience!'
            ]);
        }

        return redirect()->route('dashboard.classroom.streams.index', $classroom->code)
            ->with('success', 'Joined successfully');
    }
}
