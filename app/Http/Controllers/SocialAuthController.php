<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class SocialAuthController extends Controller
{
    private $allowedProviders = ['discord', 'twitter', 'github'];

    public function redirectToProvider($provider)
    {
        if (Auth::check()) {
            return redirect()->route('game.index');
        }

        if (!in_array($provider, $this->allowedProviders)) {
            return redirect()->route('welcome')->with('error', 'Invalid social provider.');
        }

        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback($provider)
    {
        if (!in_array($provider, $this->allowedProviders)) {
            return redirect()->route('welcome')->with('error', 'Invalid social provider.');
        }

        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
        } catch (\Exception $e) {
            Log::error("Social auth error for {$provider}: " . $e->getMessage());
            return redirect()->route('welcome')->with('error', 'Authentication failed. Please try again.');
        }

        $providerId = $socialUser->getId();
        $username = $this->generateUsername($socialUser, $provider);
        $email = $socialUser->getEmail() ?? $providerId . '@' . $provider . '.social';
        
        $user = User::firstOrCreate(
            [$provider . '_id' => $providerId],
            [
                'name' => $username,
                'email' => $email,
                'password' => bcrypt(Str::random(32)),
                'email_verified_at' => $socialUser->getEmail() ? now() : null,
            ]
        );

        Auth::login($user, true);

        if (!$user->hasVerifiedEmail() && $user->email && !str_ends_with($user->email, '.social')) {
            $user->sendEmailVerificationNotification();
            return redirect()->route('verification.notice')->with('message', 'Please verify your email address to continue.');
        }

        $apiToken = $this->authenticateWithApiForSocial($user);
        
        if (!$apiToken) {
            return redirect()->route('welcome')->with('error', 'Unable to connect to game server. Please try again.');
        }

        return redirect()->route('game.index', ['token' => $apiToken]);
    }

    private function generateUsername($socialUser, $provider)
    {
        $baseName = $socialUser->getNickname() ?? $socialUser->getName() ?? $socialUser->getEmail();
        $baseName = preg_replace('/[^a-zA-Z0-9]/', '', str_replace(' ', '', $baseName));
        
        if (strlen($baseName) < 6) {
            $baseName = $provider . $baseName;
        }

        $username = substr($baseName, 0, 20);
        $counter = 1;

        while (User::where('name', $username)->exists()) {
            $username = substr($baseName, 0, 16) . $counter;
            $counter++;
        }

        return $username;
    }

    private function authenticateWithApiForSocial($user)
    {
        try {
            $temporaryPassword = 'social_' . $user->id . '_' . time();
            
            $response = Http::post(env('API_BASE_URL') . '/api/login', [
                'username' => $user->name,
                'password' => $temporaryPassword,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['token'] ?? null;
            }

            $registerResponse = Http::post(env('API_BASE_URL') . '/api/register', [
                'username' => $user->name,
                'password' => $temporaryPassword,
            ]);

            if ($registerResponse->successful()) {
                $registerData = $registerResponse->json();
                return $registerData['token'] ?? null;
            }

            return null;
        } catch (\Exception $e) {
            Log::error("API auth error for social user {$user->name}: " . $e->getMessage());
            return null;
        }
    }
}