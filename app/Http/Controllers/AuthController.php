<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $rules = [
            'username' => 'required|string',
            'password' => 'required|string',
        ];

        if (env('TURNSTILE_ENABLED', false)) {
            $rules['cf-turnstile-response'] = 'required|string';
        }

        $request->validate($rules);

        if (env('TURNSTILE_ENABLED', false)) {
            $turnstileResponse = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => env('TURNSTILE_SECRET'),
                'response' => $request->input('cf-turnstile-response'),
            ]);

            if (!$turnstileResponse->json()['success']) {
                return back()->withErrors(['captcha' => 'Captcha validation failed.']);
            }
        }

        $apiResponse = Http::post(env('API_BASE_URL') . '/api/login', [
            'username' => $request->username,
            'password' => $request->password,
        ]);

        if (!$apiResponse->ok()) {
            $error = $apiResponse->json();
            return back()->withErrors(['login' => $error['reason'] ?? 'Login failed']);
        }

        $data = $apiResponse->json();
        if (!isset($data['token'])) {
            return back()->withErrors(['login' => 'Invalid response from API']);
        }

        $user = User::updateOrCreate(
            ['name' => $request->username],
            ['password' => bcrypt($request->password)]
        );

        Auth::login($user, $request->boolean('remember-me'));

        if (!$user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
            return redirect()->route('verification.notice')->with('message', 'Please verify your email address to continue.');
        }

        return redirect('/game?token=' . $data['token']);
    }

    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $rules = [
            'email' => 'required|email',
        ];

        if (env('TURNSTILE_ENABLED', false)) {
            $rules['cf-turnstile-response'] = 'required|string';
        }

        $request->validate($rules);

        if (env('TURNSTILE_ENABLED', false)) {
            $turnstileResponse = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => env('TURNSTILE_SECRET'),
                'response' => $request->input('cf-turnstile-response'),
            ]);

            if (!$turnstileResponse->json()['success']) {
                return back()->withErrors(['captcha' => 'Captcha validation failed.']);
            }
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetPasswordForm($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function resetPassword(Request $request)
    {
        $rules = [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ];

        if (env('TURNSTILE_ENABLED', false)) {
            $rules['cf-turnstile-response'] = 'required|string';
        }

        $request->validate($rules);

        if (env('TURNSTILE_ENABLED', false)) {
            $turnstileResponse = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => env('TURNSTILE_SECRET'),
                'response' => $request->input('cf-turnstile-response'),
            ]);

            if (!$turnstileResponse->json()['success']) {
                return back()->withErrors(['captcha' => 'Captcha validation failed.']);
            }
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => bcrypt($password)
                ])->save();

                $apiResponse = Http::post(env('API_BASE_URL') . '/api/update-password', [
                    'username' => $user->name,
                    'password' => $password,
                ]);

                if (!$apiResponse->ok()) {
                    throw new \Exception('Failed to update password in external API');
                }
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }
}