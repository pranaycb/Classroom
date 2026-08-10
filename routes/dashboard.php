<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\HomeController;
use App\Http\Controllers\Dashboard\ProfileController;
use App\Http\Controllers\Dashboard\GetStartController;
use App\Http\Controllers\Dashboard\Classroom\ExamController;
use App\Http\Controllers\Dashboard\Classroom\ClassController;
use App\Http\Controllers\Dashboard\Classroom\PeopleController;
use App\Http\Controllers\Dashboard\Classroom\ResultController;
use App\Http\Controllers\Dashboard\Classroom\StreamController;
use App\Http\Controllers\Dashboard\Classroom\CommentController;
use App\Http\Controllers\Dashboard\Classroom\QuestionController;
use App\Http\Controllers\Dashboard\Classroom\SettingsController;
use App\Http\Controllers\Dashboard\Classroom\AssistantController;
use App\Http\Controllers\Dashboard\Classroom\ClassroomController;
use App\Http\Controllers\Dashboard\Classroom\AssignmentController;
use App\Http\Controllers\Dashboard\Classroom\SubmissionController;

Route::middleware(['auth', 'verified'])->name('dashboard.')->group(function () {

    Route::get('home/{slug?}', HomeController::class)->name('index')
        ->whereIn('slug', ['created', 'joined']);

    Route::redirect('dashboard', 'home');

    /**
     * Create or join class routes
     */
    Route::prefix('getstarted')->name('getstart.')->group(function () {
        Route::get('/', [GetStartController::class, 'index'])->name('index');
        Route::post('create', [GetStartController::class, 'create'])->name('create');
        Route::post('join', [GetStartController::class, 'join'])->name('join');
        Route::get('join/{code}', [GetStartController::class, 'joinViaLink'])->name('join.link');
    });

    /**
     * Classroom
     */
    Route::prefix('classroom')->name('classroom.')->group(function () {

        Route::prefix('{classroom:code}')->group(function () {

            Route::get('download/{attachment}', [ClassroomController::class, 'download'])->name('download');

            // stream
            Route::resource('streams', StreamController::class)->except(['create', 'show']);

            // classworks
            Route::resource('assignments', AssignmentController::class);
            Route::resource('assignments.submissions', SubmissionController::class)
                ->except(['create', 'edit', 'show']);

            // participations
            Route::prefix('people')->name('people.')->group(function () {
                Route::get('/{role?}', [PeopleController::class, 'index'])
                    ->name('index')->whereIn('role', ['students', 'moderators']);
                Route::get('/requests', [PeopleController::class, 'requests'])->name('requests');
                Route::get('/activities/{user}', [PeopleController::class, 'activities'])->name('activities');
                Route::post('moderator', [PeopleController::class, 'moderator'])->name('moderation');
                Route::post('status', [PeopleController::class, 'status'])->name('action');
                Route::delete('remove/{id}', [PeopleController::class, 'destroy'])->name('destroy');

                Route::get('permissions', [PeopleController::class, 'permissions'])
                    ->name('permissions.index');
                Route::put('permissions/update', [PeopleController::class, 'updatePermission'])
                    ->name('permissions.update');
            });

            // online classes
            Route::get('online-classes/conference/{online_class}', [ClassController::class, 'conference'])
                ->name('online-classes.conference')->middleware('signed');
            Route::resource('online-classes', ClassController::class);

            // route for exam
            Route::get('exams/{exam}/portal', [ExamController::class, 'portal'])->name('exams.portal');
            Route::resource('exams', ExamController::class);
            Route::resource('exams.questions', QuestionController::class)->except(['show']);
            Route::resource('exams.results', ResultController::class)->only(['index', 'show', 'store']);

            // classroom settings
            Route::prefix('settings')->name('settings.')->group(function () {
                Route::get('/', [SettingsController::class, 'index'])->name('index');
                Route::put('update', [SettingsController::class, 'update'])->name('update');
            });

            // routes for comments
            Route::prefix('comments')->name('comments.')->group(function () {

                Route::post('store', [CommentController::class, 'storeComment'])->name('store');
                Route::delete('{comment}/delete', [CommentController::class, 'deleteComment'])->name('delete');

                Route::prefix('{comment}/reply')->name('reply.')->group(function () {
                    Route::post('store', [CommentController::class, 'storeReply'])->name('store');
                    Route::delete('{reply}/delete', [CommentController::class, 'deleteReply'])->name('delete');
                });
            })->whereIn('slug', ['stream', 'assignment'])->whereNumber('id');

            /**
             * Ai Learning Assistant
             */
            Route::prefix('assistant')->name('assistant.')->group(function () {
                Route::get('/', [AssistantController::class, 'index'])->name('index');
                Route::post('ask', [AssistantController::class, 'ask'])->name('ask');
            });
        });
    });

    /**
     * Profile update
     */
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::put('updateAvatar', [ProfileController::class, 'updateAvatar'])->name('avatar.update');
        Route::put('updateInfo', [ProfileController::class, 'updateInfo'])->name('info.update');
        Route::put('updatePassword', [ProfileController::class, 'updatePassword'])->name('password.update');
    });
});
