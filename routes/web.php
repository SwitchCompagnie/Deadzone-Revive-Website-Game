<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\ForumThreadController;
use App\Http\Controllers\ForumPostController;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use App\Models\User;

// Maintenance status API endpoint (publicly accessible)
Route::get('/api/maintenance/status', [MaintenanceController::class, 'status'])->name('maintenance.status');

Route::get('/', [AuthController::class, 'showLoginForm'])->name('welcome');

Route::middleware(['guest', 'App\Http\Middleware\CheckMaintenanceMode'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/auth/{provider}', [SocialAuthController::class, 'redirectToProvider'])->name('auth.social');
    Route::get('/password/reset', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/password/email', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/password/reset/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/password/reset', [AuthController::class, 'resetPassword'])->name('password.update');
});

// Social auth callback must be outside guest middleware to properly handle OAuth redirects
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback'])->name('auth.callback');

Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/game', function () {
        $token = \App\Http\Controllers\AuthController::getOrGenerateApiToken();

        if (!$token) {
            return redirect()->route('login')->with('error', 'Failed to authenticate with game server. Please try logging in again.');
        }

        return view('game', ['token' => $token]);
    })->middleware(['verified', 'App\Http\Middleware\CheckMaintenanceMode'])->name('game.index');
    
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');
    
    Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
        $user = User::findOrFail($id);
        
        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            abort(403);
        }
        
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('game.index')->with('message', 'Email already verified!');
        }
        
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }
        
        return redirect()->route('game.index')->with('message', 'Email verified successfully!');
    })->middleware('signed')->name('verification.verify');
    
    Route::post('/email/resend', function (Request $request) {
        if ($request->user()->hasVerifiedEmail()) {
            return back()->with('message', 'Email already verified!');
        }
        
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', 'Verification link sent!');
    })->middleware('throttle:6,1')->name('verification.send');

    Route::post('/email/verify-code', [AuthController::class, 'verifyEmailWithCode'])
        ->middleware('throttle:6,1')
        ->name('verification.verify-code');

    Route::post('/email/resend-code', [AuthController::class, 'resendVerificationCode'])
        ->middleware('throttle:6,1')
        ->name('verification.resend-code');
});

// Forum routes (public access to view, auth required to post)
Route::prefix('forum')->name('forum.')->group(function () {
    Route::get('/', [ForumController::class, 'index'])->name('index');
    Route::get('/search', [ForumController::class, 'search'])->name('search');
    Route::get('/category/{slug}', [ForumController::class, 'category'])->name('category');
    Route::get('/thread/{slug}', [ForumThreadController::class, 'show'])->name('thread.show');
    
    Route::middleware('auth')->group(function () {
        Route::get('/category/{categorySlug}/create-thread', [ForumThreadController::class, 'create'])->name('thread.create');
        Route::post('/category/{categorySlug}/create-thread', [ForumThreadController::class, 'store'])->name('thread.store');
        Route::post('/thread/{thread}/like', [ForumThreadController::class, 'like'])->name('thread.like');
        Route::post('/thread/{thread}/reply', [ForumPostController::class, 'store'])->name('post.store');
        Route::post('/post/{post}/like', [ForumPostController::class, 'like'])->name('post.like');
        Route::delete('/post/{post}', [ForumPostController::class, 'destroy'])->name('post.destroy');
    });
});