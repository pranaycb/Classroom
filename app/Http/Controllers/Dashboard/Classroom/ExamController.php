<?php

namespace App\Http\Controllers\Dashboard\Classroom;

use App\Action\Permission;
use App\Models\Exam;
use App\Models\Classroom;
use Illuminate\Http\Request;
use App\Http\Resources\ExamResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use App\Http\Controllers\Dashboard\Classroom\ClassroomController;

class ExamController extends ClassroomController implements HasMiddleware
{
    /**
     * Assign middleware to different methods
     */
    public static function middleware(): array
    {
        return [
            new Middleware('hasaccess:exam.create', only: ['create', 'store']),
            new Middleware('hasaccess:exam.update', only: ['edit', 'update']),
            new Middleware('hasaccess:exam.delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Classroom $classroom)
    {
        $exams = $classroom->exams()
            ->orderBy('id')
            ->paginate(10)
            ->onEachSide(0)
            ->withQueryString();

        $data = [
            'exams' => new ExamResource($exams),
        ];

        return $this->render('Dashboard/Classroom/Exam/Index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Classroom $classroom)
    {
        return $this->render('Dashboard/Classroom/Exam/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Classroom $classroom)
    {
        $data = $request->validate([
            'name' => 'required',
            'marks' => 'required|numeric',
            'pass_mark' => 'required|numeric|lte:marks',
            'start' => 'required|date',
            'end' => 'required|date|after:start',
        ]);

        $exam = $classroom->exams()->create($data);

        return redirect()->route('dashboard.classroom.exams.show', [$classroom->code, $exam->id])
            ->with('success', 'Exam added successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Classroom $classroom, Exam $exam)
    {
        $given = $exam->results()->where('user_id', Auth::id())->exists();

        $data = [
            'data' => [
                'name' => $exam->name,
                'startt' => $exam->start,
                'start' => $exam->start->format('M d, Y \a\t h:i a'),
                'end' => $exam->end->format('M d, Y \a\t h:i a'),
                'marks' => round($exam->marks),
                'pass_mark' => round($exam->pass_mark),
                'duration' => $exam->duration,
                'status' => $exam->status,
                'given' => $given,
            ],
        ];

        if(Permission::isStudent() && !$given && $exam->status === 'ongoing') {

            $data['link'] = route('dashboard.classroom.exams.portal', [$classroom->code, $exam->id]);
        }

        return $this->render('Dashboard/Classroom/Exam/Details', $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Classroom $classroom, Exam $exam)
    {
        $data = [
            'data' => [
                'id' => $exam->id,
                'name' => $exam->name,
                'start' => $exam->start->format('Y-m-d H:i'),
                'end' => $exam->end->format('Y-m-d H:i'),
                'marks' => round($exam->marks),
                'pass_mark' => round($exam->pass_mark),
            ],
        ];

        return $this->render('Dashboard/Classroom/Exam/Edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Classroom $classroom, Exam $exam)
    {
        $data = $request->validate([
            'name' => 'required',
            'marks' => 'required|numeric',
            'pass_mark' => 'required|numeric|lte:marks',
            'start' => 'required|date',
            'end' => 'required|date|after:start',
        ]);

        $exam->update($data);

        return redirect()->route('dashboard.classroom.exams.show', [$classroom->code, $exam->id])
            ->with('success', 'Exam added successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Classroom $classroom, Exam $exam)
    {
        $exam->delete();
        return redirect()->back()->with('success', 'Exam deleted successfully');
    }

    /**
     * Online exam page.
     */
    public function portal(Request $request, Classroom $classroom, Exam $exam)
    {
        $user = $request->user();

        abort_if(!Permission::isStudent(), 403);

        // exam is not between schedule
        abort_if(!now()->isBetween($exam->start, $exam->end), 404);

        // is exam already given
        abort_if($exam->results()->where('user_id', $user->id)->exists(), 404);

        $key = 'exam_' . $exam->id . '_user_' . $user->id . '_questions';

        $questions = cache()->remember($key, $exam->end, function() use($exam) {
            return $exam->questions()->inRandomOrder()->get()->map(fn($question) => [
                'id' => $question->id,
                'question' => $question->question,
                'right' => round($question->right, 2),
                'wrong' => round($question->wrong, 2),
                'options' => $question->options->map(fn($option) => [
                    'id' => $option->id,
                    'option' => $option->option,
                ]),
            ])->toArray();
        });

        $data = [
            'room' => $this->room($classroom),
            'exam' => [
                'id' => $exam->id,
                'name' => $exam->name,
                'end' => $exam->end,
                'mark' => round($exam->marks),
                'duration' => $exam->duration,
            ],
            'questions' => [
                'questions' => $questions,
                'total' => count($questions),
            ],
        ];

        return $this->render('Dashboard/Classroom/Exam/Portal', $data);
    }
}
