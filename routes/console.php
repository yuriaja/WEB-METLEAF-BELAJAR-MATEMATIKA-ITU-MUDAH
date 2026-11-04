<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Support\QuizSchedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('mathleaf:archive-expired-quizzes', function () {
    $count = QuizSchedule::archiveExpired();
    $this->info("$count kuis melewati deadline telah diarsipkan.");
})->purpose('Arsipkan kuis MathLeaf yang melewati deadline.');

Schedule::command('mathleaf:archive-expired-quizzes')->everyMinute()->withoutOverlapping();
