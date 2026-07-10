<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('app:generate-alpha', function () {
    $this->info('Starting alpha attendance generation...');
    \App\Helpers\AttendanceHelper::generateAlphaForAllActiveSiswa();
    $this->info('Alpha attendance generation completed!');
})->purpose('Generate alpha records for missing student attendance');

