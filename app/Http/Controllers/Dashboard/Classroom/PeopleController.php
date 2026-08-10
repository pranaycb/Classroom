<?php

namespace App\Http\Controllers\Dashboard\Classroom;

use App\Models\User;
use App\Models\Classroom;
use App\Action\Permission;
use Illuminate\Http\Request;
use App\Http\Resources\PeopleResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class PeopleController extends ClassroomController implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('hasaccess:people.view_request', only: ['requests']),
            new Middleware('hasaccess:people.approve_request', only: ['status']),
            new Middleware('hasaccess:people.remove', only: ['delete']),
            new Middleware('hasaccess:people.block', only: ['status']),
            new Middleware('hasaccess:people.view_activities', only: ['activities']),
        ];
    }

    /**
     * Show all people
     */
    public function index(Classroom $classroom, string $role = '')
    {
        $teacher = $classroom->teacher;

        $participants = match ($role) {
            'students' => $classroom->students(),
            'moderators' => $classroom->moderators(),
            default => $classroom->participants()->wherePivot('status', '!=', 'pending'),
        };

        $data = [
            'teacher' => [
                'name' => $teacher->name,
                'email' => $teacher->email,
                'profile' => $teacher->profile_path,
            ],
            'participants' => new PeopleResource(
                $participants->paginate(2)->onEachSide(0)->withQueryString()
            ),
        ];

        return $this->render('Dashboard/Classroom/People/Index', $data);
    }

    /**
     * Show all requested students
     */
    public function requests(Classroom $classroom)
    {
        $data = [
            'requests' => new PeopleResource(
                $classroom->participants()
                    ->wherePivot('status', 'pending')
                    ->paginate(2)
                    ->onEachSide(0)
                    ->withQueryString()
            ),
        ];

        return $this->render('Dashboard/Classroom/People/Request', $data);
    }

    /**
     * Make or remove as moderator
     */
    public function moderator(Request $request, Classroom $classroom)
    {
        $data = $request->validate([
            'user' => 'required|exists:users,id',
            'action' => 'required|in:add,remove'
        ]);

        $classroom->participants()->updateExistingPivot($data['user'], [
            'moderator' => match($data['action']) {
                'add' => true,
                'remove' => false,
            }
        ]);

        return redirect()->back()->with('success', 'Successfull');
    }

    /**
     * Update status
     */
    public function status(Request $request, Classroom $classroom)
    {
        $data = $request->validate([
            'user' => 'required|exists:users,id',
            'status' => 'required|in:approved,banned,rejected'
        ]);

        $classroom->participants()->updateExistingPivot($data['user'], [
            'status' => $data['status'],
        ]);

        return redirect()->back()->with('success', 'Successfull');
    }

    /**
     * Delete announcement
     */
    public function destroy(Classroom $classroom, int $id)
    {
        $classroom->participants()->detach($id);

        return redirect()->back()->with('success', 'Participant deleted successfully');
    }

    /**
     * View participants activities
     */
    public function activities(Classroom $classroom, User $user)
    {
        $data = [
            'student' => [
                'name' => $user->name,
                'email' => $user->email,
                'university' => $user->university,
            ],
            'counter' => [
                'assignment' => [
                    'count' => 0,
                    'eompleted' => 0,
                ],
                'exam' => [
                    'count' => 0,
                    'eompleted' => 0,
                ],
            ],
        ];

        $user = $classroom->students()->where('user_id', $user->id)->firstOrFail();

        $totalMarks = 0;
        $totalObtained = 0;

        $exams = $classroom->exams;
        $assignments = $classroom->assignments;

        $data['exams'] = $exams->map(function($exam) use ($user, &$totalMarks, &$totalObtained, &$data) {

            $result =  $exam->results()->where('user_id', $user->id)->first();

            $data['counter']['exam']['count'] += 1;
            $data['counter']['exam']['eompleted'] += $result ? 1 : 0;

            $totalMarks += $exam->marks;
            $totalObtained += $result?->marks ?? 0;

            return [
                'name' => $exam->name,
                'date' => $exam->start->format('M d, Y \a\t h:i a'),
                'pass_marks' => round($exam->pass_mark, 2) . '/' . round($exam->marks, 2),
                'result' => [
                    'marks' => $result ? (round($result->marks, 2) . '/' . round($exam->marks, 2)) : 'N/A',
                    'status' => $result?->status ?? 'not-given',
                ],
            ];
        });

        $data['assignments'] = $assignments->map(function ($assignment) use ($user, &$totalMarks, &$totalObtained, &$data) {

            $submission =  $assignment->submissions()->where('user_id', $user->id)->first();

            $totalMarks += $assignment->marks;
            $totalObtained += $submission?->marks ?? 0;

            $data['counter']['assignment']['count'] += 1;
            $data['counter']['assignment']['eompleted'] += $submission ? 1 : 0;

            return [
                'title' => $assignment->title,
                'due' => $assignment->due->format('M d, Y \a\t h:i a'),
                'submission' => [
                    'marks' => $submission ? (round($submission->marks, 2) . '/' . round($assignment->marks, 2)) : 'N/A',
                    'feedback' => $submission ? ($submission->feedback ?? 'N/A') : 'N/A',
                ],
            ];
        });

        $data['marks'] = [
            'total' => round($totalMarks, 2),
            'obtained' => round($totalObtained, 2),
            'percentage' => $totalMarks > 0 ? (round((($totalObtained / $totalMarks) * 100), 2)) : 0,
        ];

        return $this->render('Dashboard/Classroom/People/Activity', $data);
    }

    /**
     * View moderators permissions
     */
    public function permissions(Classroom $classroom)
    {
        abort_if(!Permission::isTeacher(), 401);

        $allPermissions =  json_decode(Storage::get('permissions/moderator.json'), true);

        $permissions = $this->merge((array) $classroom->moderator_permissions, $allPermissions);

        $data = [
            'permissions' => $permissions,
        ];

        return $this->render('Dashboard/Classroom/People/Permission', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function updatePermission(Request $request, Classroom $classroom)
    {
        abort_if(!Permission::isTeacher(), 401);

        $request->validate([
            'permissions' => 'required|array',
            'permissions.*.*' => 'boolean',
        ]);

        $classroom->update([
            'moderator_permissions' => $request->permissions,
        ]);

        return redirect()->back()->with('success', 'Permissions updated successfully');
    }

    /**
     * Merge permissions
     */
    protected function merge(array $arr1, array $arr2): array
    {
        $merged = collect($arr2)->map(function ($subArray, $key) use ($arr1) {
            return collect($subArray)->map(fn($v, $k) => $arr1[$key][$k] ?? false);
        });

        return $merged->toArray();
    }
}
