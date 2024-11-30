<?php

use App\Http\Middleware\AuthMiddleware;
use App\Models\Tryouts;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $tryout = Tryouts::first();
    return redirect()->route('tryout.index');
});
Route::middleware([AuthMiddleware::class])->get('/tryout-index', [App\Http\Controllers\TryoutController::class, 'index'])->name('tryout.index');
Route::middleware([AuthMiddleware::class])->post('/tryout-get', [App\Http\Controllers\TryoutController::class, 'getTryouts'])->name('tryouts.getTryouts');
Route::middleware([AuthMiddleware::class])->get('/tryout/show/{id}', [App\Http\Controllers\TryoutController::class, 'show'])->name('tryouts.show');
Route::middleware([AuthMiddleware::class])->get('/tryout/hasil/{id}', [App\Http\Controllers\TryoutController::class, 'hasil'])->name('tryouts.hasil');
Route::middleware([AuthMiddleware::class])->get('/tryout/pembahasan/{id}', [App\Http\Controllers\TryoutController::class, 'pembahasan'])->name('tryouts.pembahasan');
Route::middleware([AuthMiddleware::class])->post('/tryout/getQuestions', [App\Http\Controllers\TryoutController::class, 'getQuestions'])->name('tryout.getQuestions');
Route::middleware([AuthMiddleware::class])->post('/tryout/saveAnswer', [App\Http\Controllers\TryoutController::class, 'saveAnswer'])->name('tryout.saveAnswer');
Route::middleware([AuthMiddleware::class])->post('/tryout/saveTimeLeft', [App\Http\Controllers\TryoutController::class, 'saveTimeLeft'])->name('tryout.saveTimeLeft');
Route::middleware([AuthMiddleware::class])->get('/tryout/getTimeLeft', [App\Http\Controllers\TryoutController::class, 'getTimeLeft'])->name('tryout.getTimeLeft');


Route::middleware('guest')->get('/login', [App\Http\Controllers\AuthController::class, 'login'])->name('login');
Route::middleware('guest')->post('/login', [App\Http\Controllers\AuthController::class, 'loginStore'])->name('loginStore');
