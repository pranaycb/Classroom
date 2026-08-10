<?php

namespace App\Http\Controllers\Dashboard\Classroom;

use App\Models\Exam;
use Inertia\Inertia;
use App\Models\Classroom;
use App\Models\ExamQuestion;
use Illuminate\Http\Request;
use App\Http\Resources\QuestionResource;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class QuestionController extends ClassroomController implements HasMiddleware
{
    /**
     * Assign middleware to different methods
     */
    public static function middleware(): array
    {
        return [
            new Middleware('hasaccess:question.view', only: ['index']),
            new Middleware('hasaccess:question.create', only: ['create', 'store']),
            new Middleware('hasaccess:question.update', only: ['edit', 'update']),
            new Middleware('hasaccess:question.delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Classroom $classroom, Exam $exam)
    {
        $questions = $exam->questions()
            ->paginate(30)
            ->onEachSide(0)
            ->withQueryString();

        $marksAdded = $exam->questions()->sum('right');

        $data = [
            'exam' => [
                'id' => $exam->id,
                'name' => $exam->name,
                'start' => $exam->start->format('M d, Y \a\t h:i a'),
                'duration' => $exam->duration,
                'marks' => round($marksAdded) . '/' . round($exam->marks),
                'addMore' => $marksAdded < $exam->marks,
            ],
            'questions' => Inertia::defer(fn() => new QuestionResource($questions)),
        ];

        return $this->render('Dashboard/Classroom/Exam/Question/Index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Classroom $classroom, Exam $exam)
    {
        $canAdd = $exam->questions()->sum('right') < $exam->marks;

        abort_if(!$canAdd, 404);

        $data = [
            'exam' => [
                'id' => $exam->id,
                'name' => $exam->name,
            ],
        ];

        return $this->render('Dashboard/Classroom/Exam/Question/Create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Classroom $classroom, Exam $exam)
    {
        $request->validate([
            'question' => 'required',
            'right' => 'required|numeric',
            'wrong' => 'nullable|numeric',
            'options.*.option' => 'required',
            'options.*.correct' => 'required|boolean',
        ], messages: [
            'options.*.option.required' => 'The option field is required',
        ]);

        $added = $exam->questions()->sum('right');

        $canAdd = $added < $exam->marks;

        // if total marks exceeds
        if(!$canAdd) {
            return redirect()->back()->withErrors(['error' => 'Total marks exceeds']);
        }

        $question = $exam->questions()->create([
            'question' => $request->question,
            'right' => $request->right,
            'wrong' => $request->wrong ?? 0,
        ]);

        $options = collect($request->options)->map(fn($option) => [
            'option' => $option['option'],
            'correct' => $option['correct'] ?? false,
        ])->toArray();

        $question->options()->createMany($options);

        if(($added + $request->right) == $exam->marks) {
            return redirect()
                ->route('dashboard.classroom.exams.questions.index', [$classroom->code, $exam->id])
                ->with('success', 'All questions are added successfully');
        }

        return redirect()->back()->with('success', 'Added successfully');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Classroom $classroom, Exam $exam, ExamQuestion $examQuestion)
    {
        $data = [
            'exam' => [
                'id' => $exam->id,
                'name' => $exam->name,
            ],
            'data' => [
                'id' => $examQuestion->id,
                'question' => $examQuestion->question,
                'right' => round($examQuestion->right, 2),
                'wrong' => round($examQuestion->wrong, 2),
                'options' => $examQuestion->options->map(fn($option) => [
                    'id' => $option['id'],
                    'option' => $option['option'],
                    'correct' => $option['correct'],
                ]),
            ]
        ];

        return $this->render('Dashboard/Classroom/Exam/Question/Edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Classroom $classroom, Exam $exam, ExamQuestion $examQuestion)
    {
        $request->validate([
            'question' => 'required',
            'right' => 'required|numeric',
            'wrong' => 'nullable|numeric',
            'options.*.option' => 'required',
            'options.*.correct' => 'required|boolean',
        ], messages: [
            'options.*.option.required' => 'The option field is required',
        ]);

        $totalAdded = $exam->questions()->sum('right');

        $canAdd = (($totalAdded - $examQuestion->right) + $request->right) <= $exam->marks;

        // if total marks exceeds
        if (!$canAdd) {
            return redirect()->back()->withErrors(['error' => 'Total marks exceeds']);
        }

        $examQuestion->update([
            'question' => $request->question,
            'right' => $request->right,
            'wrong' => $request->wrong ?? 0,
        ]);

        collect($request->options)->each(function ($option) use ($examQuestion) {

            $examQuestion->options()->updateOrCreate(
                [
                    'id' => $option['id'] ?? null,
                ],
                [
                    'option' => $option['option'],
                    'correct' => $option['correct'] ?? false,
                ]
            );
        });

        // delete the removed ids
        $examQuestion->options()
            ->whereNotIn('id', collect($request->options)->pluck('id'))
            ->delete();

        return redirect()
            ->route('dashboard.classroom.exams.questions.index', [$classroom->code, $exam->id])
            ->with('success', 'Updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Classroom $classroom, Exam $exam, ExamQuestion $examQuestion)
    {
        $examQuestion->delete();
        return redirect()->back()->with('success', 'Deleted successfully');
    }
}
