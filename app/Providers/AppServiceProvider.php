<?php

namespace App\Providers;

use App\Models\Announcement;
use App\Models\ExamQuestion;
use Illuminate\Support\Facades\Vite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        // auto eager load relationship
        Model::automaticallyEagerLoadRelationships();

        // bind question with exam question
        Route::bind('question', function($question) {
            return ExamQuestion::findOrFail($question);
        });

        // bind stream parameter with announcement
        Route::bind('stream', function(string $stream) {
            return Announcement::findOrFail($stream);
        });
    }
}
