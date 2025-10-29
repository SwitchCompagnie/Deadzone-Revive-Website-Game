<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    private $allowedProviders = ['discord', 'twitter', 'github'];

    public function redirectToProvider($provider)
    {
        if (Auth::check()) {
            return redirect()->route('game.index');
        }

        if (! in_array($provider, $this->allowedProviders)) {
            return redirect()->route('welcome')->with('error', 'Invalid social provider.');
        }

        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback($provider)
    {
        if (! in_array($provider, $this->allowedProviders)) {
            return redirect()->route('welcome')->with('error', 'Invalid social provider.');
        }

        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
        } catch (\Exception $e) {
            Log::error("Social auth error for {$provider}: ".$e->getMessage());

            return redirect()->route('welcome')->with('error', 'Authentication failed. Please try again.');
        }

        $providerId = $socialUser->getId();
        $username = $this->generateUsername($socialUser, $provider);
        $email = $socialUser->getEmail() ?? $providerId.'@'.$provider.'.social';

        $user = User::where($provider.'_id', $providerId)->first();

        $isNewUser = false;
        if (! $user) {
            $isNewUser = true;
            $randomPassword = Str::random(32);
            $user = User::create([
                'name' => $username,
                'email' => $email,
                $provider.'_id' => $providerId,
                'password' => bcrypt($randomPassword),
                'random_password' => $randomPassword,
                'email_verified_at' => $socialUser->getEmail() ? now() : null,
            ]);

            Http::post(env('API_BASE_URL').'/api/register', [
                'username' => $user->name,
                'password' => $randomPassword,
            ]);
        }

        Auth::login($user, true);

        // Regenerate session for security
        request()->session()->regenerate();

        if (! $user->hasVerifiedEmail() && $user->email && ! str_ends_with($user->email, '.social')) {
            $code = $user->generateEmailVerificationCode();
            $user->notify(new \App\Notifications\EmailVerificationCode($code));

            return redirect()->route('verification.notice')->with('message', 'Please verify your email address to continue.');
        }

        $apiToken = $this->authenticateWithApiForSocial($user);

        if (! $apiToken) {
            Auth::logout();

            return redirect()->route('welcome')->with('error', 'Unable to connect to game server. Please try again.');
        }

        // Set flash message like regular login
        $message = $isNewUser
            ? 'Account created successfully! Welcome to Deadzone.'
            : 'Logged in successfully via '.ucfirst($provider).'!';

        return redirect()->route('game.index', ['token' => $apiToken])->with('status', $message);
    }

    private function generateUsername($socialUser, $provider)
    {
        $baseName = $socialUser->getNickname() ?? $socialUser->getName() ?? $socialUser->getEmail();
        $baseName = preg_replace('/[^a-zA-Z0-9]/', '', str_replace(' ', '', $baseName));

        if (strlen($baseName) < 6) {
            $baseName = $provider.$baseName;
        }

        $username = substr($baseName, 0, 20);
        $counter = 1;

        while (User::where('name', $username)->exists()) {
            $username = substr($baseName, 0, 16).$counter;
            $counter++;
        }

        return $username;
    }

    private function authenticateWithApiForSocial($user)
    {
        try {
            $password = $user->random_password;

            $response = Http::post(env('API_BASE_URL').'/api/login', [
                'username' => $user->name,
                'password' => $password,
            ]);

            if ($response->successful()) {
                return $response->json()['token'] ?? null;
            }

            return null;
        } catch (\Exception $e) {
            Log::error("API auth error for social user {$user->name}: ".$e->getMessage());

            return null;
        }
    }
}
