<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/', [App\Http\Controllers\ResumeMatcherController::class, 'index'])->name('resume-matcher');
Route::post('/resume-matcher/analyze', [App\Http\Controllers\ResumeMatcherController::class, 'analyze'])->name('resume-matcher.analyze');
