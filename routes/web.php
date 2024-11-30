<?php

use App\Http\Middleware\AuthMiddleware;
use App\Models\Tryouts;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $tryout = Tryouts::first();
    return redirect()->route('tryouts-tes', ['id' => $tryout->id]);
});
Route::middleware([AuthMiddleware::class])->get('/tryouts-tes/{id}', [App\Http\Controllers\TryoutController::class, 'index'])->name('tryouts-tes');
Route::middleware([AuthMiddleware::class])->post('/soal', [App\Http\Controllers\TryoutController::class, 'getQuestion'])->name('soal');
Route::middleware([AuthMiddleware::class])->post('/tryouts/save-endtime', [App\Http\Controllers\TryoutController::class, 'saveEndTime'])->name('tryouts.saveEndTime');
Route::middleware([AuthMiddleware::class])->get('/tryouts/get-endtime', [App\Http\Controllers\TryoutController::class, 'getEndTime'])->name('tryouts.getEndTime');
Route::get('/tryout-index', [App\Http\Controllers\TryoutController::class, 'index'])->name('tryout.index');
Route::post('/tryout-get', [App\Http\Controllers\TryoutController::class, 'getTryouts'])->name('tryouts.getTryouts');
Route::get('/tryout/show/{id}', [App\Http\Controllers\TryoutController::class, 'show'])->name('tryouts.show');
Route::get('/tryout/hasil/{id}', [App\Http\Controllers\TryoutController::class, 'hasil'])->name('tryouts.hasil');
Route::get('/tryout/pembahasan/{id}', [App\Http\Controllers\TryoutController::class, 'pembahasan'])->name('tryouts.pembahasan');
Route::post('/tryout/getQuestions', [App\Http\Controllers\TryoutController::class, 'getQuestions'])->name('tryout.getQuestions');
Route::post('/tryout/saveAnswer', [App\Http\Controllers\TryoutController::class, 'saveAnswer'])->name('tryout.saveAnswer');


Route::get('/login', [App\Http\Controllers\AuthController::class, 'login'])->name('login');
Route::post('/login', [App\Http\Controllers\AuthController::class, 'loginStore'])->name('loginStore');
