<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('game.index');
        }

        return view('welcome', [
            'maintenanceMode' => \App\Models\Setting::isMaintenanceMode(),
        ]);
    }

    public function login(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('game.index');
        }

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|min:6|regex:/^[a-zA-Z0-9]+$/',
            'password' => 'required|string|min:6',
            'cf-turnstile-response' => env('TURNSTILE_ENABLED', false) ? 'required|string' : '',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            return back()->withErrors($validator)->withInput();
        }

        if (env('TURNSTILE_ENABLED', false)) {
            $turnstileValid = $this->validateTurnstile($request->input('cf-turnstile-response'));
            if (! $turnstileValid) {
                if ($request->wantsJson()) {
                    return response()->json(['errors' => ['captcha' => ['Captcha validation failed.']]], 422);
                }

                return back()->withErrors(['captcha' => 'Captcha validation failed.'])->withInput();
            }
        }

        $apiToken = $this->authenticateWithApi($request->username, $request->password);

        if (! $apiToken) {
            if ($request->wantsJson()) {
                return response()->json(['errors' => ['login' => ['Invalid credentials or API error.']]], 401);
            }

            return back()->withErrors(['login' => 'Invalid credentials or API error.'])->withInput();
        }

        $user = User::firstOrCreate(
            ['name' => $request->username],
            ['password' => bcrypt($request->password)]
        );

        Auth::login($user, $request->boolean('remember-me'));

        // Regenerate session for security
        $request->session()->regenerate();

        // Store API token in session
        $request->session()->put('api_token', $apiToken);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'token' => $apiToken,
                'redirect' => route('game.index'),
            ]);
        }

        return redirect()->intended(route('game.index'))->with('status', 'Logged in successfully!');
    }

    public function showForgotPasswordForm()
    {
        if (Auth::check()) {
            return redirect()->route('game.index');
        }

        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'cf-turnstile-response' => env('TURNSTILE_ENABLED', false) ? 'required|string' : '',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        if (env('TURNSTILE_ENABLED', false)) {
            $turnstileValid = $this->validateTurnstile($request->input('cf-turnstile-response'));
            if (! $turnstileValid) {
                return back()->withErrors(['captcha' => 'Captcha validation failed.'])->withInput();
            }
        }

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetPasswordForm($token)
    {
        if (Auth::check()) {
            return redirect()->route('game.index');
        }

        return view('auth.reset-password', ['token' => $token]);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
            'cf-turnstile-response' => env('TURNSTILE_ENABLED', false) ? 'required|string' : '',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        if (env('TURNSTILE_ENABLED', false)) {
            $turnstileValid = $this->validateTurnstile($request->input('cf-turnstile-response'));
            if (! $turnstileValid) {
                return back()->withErrors(['captcha' => 'Captcha validation failed.'])->withInput();
            }
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill(['password' => bcrypt($password)])->save();
                $apiResponse = Http::post(env('API_BASE_URL').'/api/update-password', [
                    'username' => $user->name,
                    'password' => $password,
                ]);
                if (! $apiResponse->ok()) {
                    throw new \Exception('Failed to update password in external API');
                }
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('welcome')->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function resendVerificationCode(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return back()->with('message', 'Email already verified!');
        }

        $code = $user->generateEmailVerificationCode();
        $user->notify(new \App\Notifications\EmailVerificationCode($code));

        return back()->with('message', 'Verification code sent!');
    }

    public function verifyEmailWithCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $user = $request->user();

        if ($user->verifyEmailWithCode($request->code)) {
            return redirect()->route('game.index')->with('status', 'Email verified successfully!');
        }

        return back()->withErrors(['code' => 'Invalid or expired verification code.']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('welcome');
    }

    private function validateTurnstile($token)
    {
        $response = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret' => env('TURNSTILE_SECRET'),
            'response' => $token,
        ]);

        return $response->successful() && $response->json()['success'] === true;
    }

    private function authenticateWithApi($username, $password)
    {
        try {
            $response = Http::post(env('API_BASE_URL').'/api/login', [
                'username' => $username,
                'password' => $password,
            ]);
            if ($response->successful()) {
                $data = $response->json();

                return $data['token'] ?? null;
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get or generate API token for authenticated user
     *
     * @return string|null
     */
    public static function getOrGenerateApiToken()
    {
        $user = auth()->user();

        if (!$user) {
            return null;
        }

        // Check if token exists in session and is still valid
        $token = session('api_token');

        if ($token) {
            return $token;
        }

        // Generate new token by authenticating with API
        try {
            // Use the stored password or random_password for social logins
            $password = $user->random_password ?? $user->password;

            $response = Http::post(env('API_BASE_URL').'/api/login', [
                'username' => $user->name,
                'password' => $password,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $token = $data['token'] ?? null;

                if ($token) {
                    session(['api_token' => $token]);
                    return $token;
                }
            }

            return null;
        } catch (\Exception $e) {
            \Log::error('Failed to generate API token: ' . $e->getMessage());
            return null;
        }
    }
}
