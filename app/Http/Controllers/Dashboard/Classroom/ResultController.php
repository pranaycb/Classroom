<?php

namespace App\Http\Controllers\Dashboard\Classroom;

use App\Models\Exam;
use Inertia\Inertia;
use App\Models\Result;
use App\Models\Classroom;
use Illuminate\Http\Request;
use App\Models\QuestionAnswer;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\ResultResource;
use Illuminate\Routing\Controllers\HasMiddleware;

class ResultController extends ClassroomController implements HasMiddleware
{
    /**
     * Assign middleware to different methods
     */
    public static function middleware(): array
    {
        return [
            // new Middleware('hasaccess:question.create', only: ['create', 'store']),
            // new Middleware('hasaccess:question.read', only: ['index', 'show']),
            // new Middleware('hasaccess:question.update', only: ['edit', 'update']),
            // new Middleware('hasaccess:question.delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Classroom $classroom, Exam $exam)
    {
        // exam needs to end to view result
        abort_if(now()->lte($exam->end), 404);

        $questions = $exam->results()
            ->paginate(30)
            ->onEachSide(0)
            ->withQueryString();

        $data = [
            'room' => $this->room($classroom),
            'exam' => [
                'id' => $exam->id,
                'name' => $exam->name,
                'pass_mark' => round($exam->pass_mark, 2) . '/' . round($exam->marks, 2),
            ],
            'results' => Inertia::defer(fn() => new ResultResource($questions)),
        ];

        return Inertia::render('Dashboard/Classroom/Exam/Result/Index', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Classroom $classroom, Exam $exam)
    {
        $request->validate([
            'answers.*.question' => 'required|numeric|exists:exam_questions,id',
            'answers.*.answer' => 'nullable|numeric|exists:question_options,id',
        ]);

        $user = $request->user();

        // is exam already given
        abort_if($exam->results()->where('user_id', $user->id)->exists(), 404);

        $total = 0;
        $questions = $exam->questions->keyBy('id');

        $data = collect($request->answers)->map(function($answer) use($user, $questions, &$total) {

            $question = $questions[$answer['question']];

            // check if answered right or wrong
            if (!empty($answer['answer'])) {
                $option = $question->options->firstWhere('id', $answer['answer']);
                $total += $option->correct ? $question->right : -$question->wrong;
            }

            return [
                'user_id' => $user->id,
                'exam_question_id' => $question->id,
                'question_option_id' => $answer['answer'],
            ];
        })->toArray();

        DB::transaction(function () use ($data, $exam, $user, $total) {

            // insert students answers  or update if already exist
            QuestionAnswer::upsert(
                $data,
                uniqueBy: ['user_id', 'exam_question_id'],
                update: ['question_option_id']
            );

            // add result
            $exam->results()->create([
                'user_id' => $user->id,
                'marks' => $total,
            ]);
        });

        return redirect()->route('dashboard.classroom.exams.show', [$classroom->code, $exam->id])
            ->with('success', 'Your answer has been successfully submitted');
    }

    /**
     * Show the specified resource.
     */
    public function show(Classroom $classroom, Exam $exam, Result $result)
    {
        // exam needs to end to view result
        abort_if(now()->lte($exam->end), 404);

        $answers = QuestionAnswer::query()
            ->where('user_id', $result->user_id)
            ->whereRelation('examQuestion', 'exam_id', $exam->id)
            ->get()->map(function ($answer) {

                $answered = !is_null($answer->question_option_id);

                return [
                    'mark' => !$answered ? 0 : (
                        $answer->questionOption->correct ?
                        round($answer->examQuestion->right, 2) :
                        round($answer->examQuestion->wrong, 2)
                    ),
                    'attempted' => $answered,
                    'correct' => $answer->questionOption?->correct,
                    'question' => [
                        'ques' => $answer->examQuestion->question,
                        'options' => $answer->examQuestion->options->map(fn($opt) => [
                            'option' => $opt->option,
                            'answered' => $opt->id === $answer->question_option_id,
                            'correct' => $opt->correct
                        ]),
                    ]
                ];
            });

        $data = [
            'room' => $this->room($classroom),
            'exam' => [
                'id' => $exam->id,
                'name' => $exam->name,
            ],
            'result' => [
                'student' => $result->user->name,
                'mark' => round($result->marks, 2) . '/' . round($result->exam->marks, 2),
                'pass_mark' => round($result->exam->pass_mark, 2) . '/' . round($result->exam->marks, 2),
                'right' => (clone $answers)->where('correct', true)->count(),
                'wrong' => (clone $answers)->where('correct', false)->count(),
                'status' => $result->status,
                'highest' => round(Result::where('exam_id', $exam->id)->max('marks'), 2) . ' / ' . round($exam->marks),
            ],
            'answers' => $answers,
        ];

        return Inertia::render('Dashboard/Classroom/Exam/Result/Details', $data);
    }
}
