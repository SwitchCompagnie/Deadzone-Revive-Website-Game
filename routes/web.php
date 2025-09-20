<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SocialAuthController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/auth/{provider}', [SocialAuthController::class, 'redirectToProvider'])->name('auth.social');
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback']);

Route::get('/game', function () {
    return view('game');
})->name('game.index');