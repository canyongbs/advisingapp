<?php

use AdvisingApp\Ai\Http\Controllers\QnAAdvisorController;
use AdvisingApp\Ai\Http\Middleware\EnsureQnAAdvisorIsEmbeddableAndAuthorized;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/qna-advisor/{qnAAdvisor}/widgets', QnAAdvisorController::class)
    ->middleware([
        'signed:relative',
        EnsureQnAAdvisorIsEmbeddableAndAuthorized::class,
    ])
    ->name('qna-advisor.widget');
