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
    private function redirectIfAuthenticated()
    {
        if (Auth::check()) {
            return redirect()->route('game.index');
        }
        return null;
    }

    private function validateTurnstileIfEnabled($token)
    {
        if (!env('TURNSTILE_ENABLED', false)) {
            return true;
        }

        return $this->validateTurnstile($token);
    }

    public function showLoginForm()
    {
        if ($redirect = $this->redirectIfAuthenticated()) {
            return $redirect;
        }

        return view('welcome', [
            'maintenanceMode' => \App\Models\Setting::isMaintenanceMode(),
        ]);
    }

    public function login(Request $request)
    {
        if ($redirect = $this->redirectIfAuthenticated()) {
            return $redirect;
        }

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|min:6|regex:/^[a-zA-Z0-9]+$/',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6',
            'cf-turnstile-response' => env('TURNSTILE_ENABLED', false) ? 'required|string' : '',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            return back()->withErrors($validator)->withInput();
        }

        if (!$this->validateTurnstileIfEnabled($request->input('cf-turnstile-response'))) {
            if ($request->wantsJson()) {
                return response()->json(['errors' => ['captcha' => ['Captcha validation failed.']]], 422);
            }

            return back()->withErrors(['captcha' => 'Captcha validation failed.'])->withInput();
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
            [
                'email' => $request->email,
                'password' => bcrypt($request->password)
            ]
        );

        // Update email if user already exists but email is missing or different
        if ($user->wasRecentlyCreated === false && ($user->email !== $request->email || is_null($user->email))) {
            $user->update(['email' => $request->email]);
        }

        // Send verification code for new users or users with unverified emails
        if ($user->wasRecentlyCreated || !$user->hasVerifiedEmail()) {
            $code = $user->generateEmailVerificationCode();
            $user->notify(new \App\Notifications\EmailVerificationCode($code));
        }

        Auth::login($user, $request->boolean('remember-me'));

        $request->session()->regenerate();

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
        if ($redirect = $this->redirectIfAuthenticated()) {
            return $redirect;
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

        if (!$this->validateTurnstileIfEnabled($request->input('cf-turnstile-response'))) {
            return back()->withErrors(['captcha' => 'Captcha validation failed.'])->withInput();
        }

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetPasswordForm($token)
    {
        if ($redirect = $this->redirectIfAuthenticated()) {
            return $redirect;
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

        if (!$this->validateTurnstileIfEnabled($request->input('cf-turnstile-response'))) {
            return back()->withErrors(['captcha' => 'Captcha validation failed.'])->withInput();
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

    public function showGame()
    {
        $token = self::getOrGenerateApiToken();

        if (!$token) {
            return redirect()->route('login')->with('error', 'Failed to authenticate with game server. Please try logging in again.');
        }

        return view('game', ['token' => $token]);
    }

    public function showVerifyEmailNotice()
    {
        return view('auth.verify-email');
    }

    public function verifyEmail(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            abort(403);
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('game.index')->with('message', 'Email already verified!');
        }

        if ($user->markEmailAsVerified()) {
            event(new \Illuminate\Auth\Events\Verified($user));
        }

        return redirect()->route('game.index')->with('message', 'Email verified successfully!');
    }

    public function resendVerificationEmail(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return back()->with('message', 'Email already verified!');
        }

        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', 'Verification link sent!');
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
     * Get API token for authenticated user
     *
     * @return string|null
     */
    public static function getOrGenerateApiToken()
    {
        $user = auth()->user();

        if (!$user) {
            return null;
        }

        $token = session('api_token');

        if ($token) {
            return $token;
        }

        // If no token in session, user must log in again
        \Log::warning('No API token in session for user: ' . $user->name);
        return null;
    }
}
