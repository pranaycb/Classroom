<?php

namespace App\Http\Controllers\Dashboard\Classroom;

use App\Action\Permission;
use Inertia\Inertia;
use Firebase\JWT\JWT;
use Pest\Support\Str;
use App\Models\Classroom;
use App\Models\OnlineClass;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use App\Http\Resources\OnlineClassResource;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class ClassController extends ClassroomController implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('hasaccess:class.create', only: ['create', 'store']),
            new Middleware('hasaccess:class.update', only: ['edit', 'update']),
            new Middleware('hasaccess:class.delete', only: ['delete']),
        ];
    }

    /**
     * Show all classes
     */
    public function index(Classroom $classroom)
    {
        $classes = $classroom->classes()
            ->orderByRaw('CASE WHEN end > ? THEN 0 ELSE 1 END', [ now() ])
            ->orderBy('start', 'asc')
            ->paginate(10)
            ->onEachSide(0)
            ->withQueryString();

        $data = [
            'classes' => new OnlineClassResource($classes),
        ];

        return $this->render('Dashboard/Classroom/Class/Index', $data);
    }

    /**
     * Create a class
     */
    public function create(Classroom $classroom)
    {
        return $this->render('Dashboard/Classroom/Class/Create');
    }

    /**
     * Store a created class record
     */
    public function store(Request $request, Classroom $classroom)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start' => 'required|date',
            'end' => 'nullable|date|after:start'
        ]);

        $class = $classroom->classes()->create([
            'roomid' => Str::random(7),
            'name' => $request->name,
            'start' => $request->start,
            'end' => $request->end ?? Carbon::parse($request->start)->addHour(1),
        ]);

        return redirect()->route('dashboard.classroom.online-classes.show', [$classroom->code, $class->id])
            ->with('success', 'Class added successfully');
    }

    /**
     * Show a specific class
     */
    public function show(Classroom $classroom, OnlineClass $onlineClass)
    {
        $data = [
            'data' => [
                'name' => $onlineClass->name,
                'start' => $onlineClass->start->format('M d, Y \a\t h:i a'),
                'end' => $onlineClass->end->format('M d, Y \a\t h:i a'),
                'duration' => $onlineClass->duration,
            ],
        ];

        if(Permission::isStudent() || Permission::has('class.join')) {

            $data['link'] = now()->isBetween($onlineClass->start, $onlineClass->end) ?
                URL::temporarySignedRoute('dashboard.classroom.online-classes.conference', $onlineClass->end, [
                    $classroom->code,
                    $onlineClass->id
                ])
                : null;
        }

        return $this->render('Dashboard/Classroom/Class/Details', $data);
    }

    /**
     * Edit a class
     */
    public function edit(Classroom $classroom, OnlineClass $onlineClass)
    {
        $data = [
            'data' => [
                'id' => $onlineClass->id,
                'name' => $onlineClass->name,
                'start' => $onlineClass->start->format('Y-m-d H:i'),
                'end' => $onlineClass->end->format('Y-m-d H:i'),
            ],
        ];

        return $this->render('Dashboard/Classroom/Class/Edit', $data);
    }

    /**
     * Update a class
     */
    public function update(Request $request, Classroom $classroom, OnlineClass $onlineClass)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start' => 'required|date',
            'end' => 'nullable|date|after:start'
        ]);

        $onlineClass->update([
            'name' => $request->name,
            'start' => $request->start,
            'end' => $request->end ?? Carbon::parse($request->start)->addHour(1),
        ]);

        return redirect()->route('dashboard.classroom.online-classes.show', [$classroom->code, $onlineClass->id])
            ->with('success', 'Class updated successfully');
    }

    /**
     * Delete a class
     */
    public function destroy(Classroom $classroom, OnlineClass $onlineClass)
    {
        $onlineClass->delete();

        return redirect()->back()->with('success', 'Announcement deleted successfully');
    }

    /**
     * Video conference
     */
    public function conference(Request $request, Classroom $classroom, OnlineClass $onlineClass)
    {
        abort_if(!(Permission::isStudent() || Permission::has('class.join')), 403);

        $room = $this->room($classroom);
        $roomName = $onlineClass->roomid;
        $user = $request->user();

        $token = $this->generateToken(
            roomName: $roomName,
            moderator: in_array($room['role'], ['teacher', 'moderator']),
            user: $user,
            end: $onlineClass->end
        );

        $data = [
            'room' => $room,
            'data' => [
                'id' => $onlineClass->id,
                'room' => $roomName,
                'appId' => env('JAAS_APP_ID'),
                'token' => $token,
            ],
        ];

        return $this->render('Dashboard/Classroom/Class/Conference', $data);
    }

    /**
     * Generate jaas jwt token
     */
    protected function generateToken($roomName, $moderator, $user, $end)
    {
        $appId = env('JAAS_APP_ID');
        $apiKey = env('JAAS_API_KEY');
        $privateKey = file_get_contents(storage_path('app/private/jaas-private.pk'));

        $now = time();

        $payload = [
            "aud" => "jitsi",
            "iss" =>"chat",
            "iat" => $now,
            "nbf" => $now,
            "exp" => Carbon::parse($end)->timestamp,
            "sub" => $appId,
            "room" => $roomName,
            "context" => [
                "features" => [
                    "livestreaming" => $moderator,
                    "transcription" => true,
                    "list-visitors" => $moderator,
                    "recording" => $moderator,
                    "outbound-call" => false,
                    "sip-outbound-call" => false,
                    "invite" => false,
                    'start-with-moderator' => true,
                    "end-to-leave" => true
                ],
                "user" => [
                    "id" => $user->id,
                    "moderator" => $moderator,
                    "avatar" => $user->profile_path,
                    "name" => $user->name,
                    "email" => $user->email,
                ],
            ],
        ];

        return JWT::encode($payload, $privateKey, 'RS256', $apiKey);
    }
}
