<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SocialAuthController extends Controller
{
    private $allowedProviders = ['discord', 'twitter', 'github'];

    public function redirectToProvider($provider)
    {
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
        
        $user = User::where($provider . '_id', $providerId)->first();

        if (!$user) {
            $password = Str::random(32);
            
            $apiToken = $this->registerWithApi($username, $password);
            
            if (!$apiToken) {
                return redirect()->route('welcome')->with('error', 'Failed to register with game server.');
            }

            $user = User::create([
                $provider . '_id' => $providerId,
                'name' => $username,
                'email' => $email,
                'password' => bcrypt($password),
                'email_verified_at' => $socialUser->getEmail() ? now() : null,
            ]);
        } else {
            $apiToken = $this->authenticateWithApi($user->name);
            
            if (!$apiToken) {
                return redirect()->route('welcome')->with('error', 'Failed to authenticate with game server.');
            }
        }

        Auth::login($user, true);

        if (!$user->hasVerifiedEmail() && $user->email && !str_ends_with($user->email, '.social')) {
            $user->sendEmailVerificationNotification();
            return redirect()->route('verification.notice')->with('message', 'Please verify your email address.');
        }

        return redirect('/game?token=' . $apiToken);
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

    private function registerWithApi($username, $password)
    {
        try {
            $response = Http::post(env('API_BASE_URL') . '/api/login', [
                'username' => $username,
                'password' => $password,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['token'] ?? null;
            }

            Log::error("API registration failed for {$username}: " . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error("API registration exception: " . $e->getMessage());
            return null;
        }
    }

    private function authenticateWithApi($username)
    {
        try {
            $user = User::where('name', $username)->first();
            if (!$user) {
                return null;
            }

            $tempPassword = Str::random(32);
            $user->password = bcrypt($tempPassword);
            $user->save();

            $response = Http::post(env('API_BASE_URL') . '/api/login', [
                'username' => $username,
                'password' => $tempPassword,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['token'] ?? null;
            }

            return null;
        } catch (\Exception $e) {
            Log::error("API authentication exception: " . $e->getMessage());
            return null;
        }
    }
}