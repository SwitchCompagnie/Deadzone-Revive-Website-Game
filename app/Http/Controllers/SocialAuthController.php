<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class SocialAuthController extends Controller
{
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
        } catch (\Exception $e) {
            return redirect('/')->with('error', 'Authentication failed. Please try again.');
        }

        $providerId = $socialUser->getId();
        $username = $socialUser->getNickname() ?? $socialUser->getName() ?? $socialUser->getEmail();
        
        $username = preg_replace('/[^a-zA-Z0-9_-]/', '', str_replace(' ', '_', $username));
        
        $password = Str::random(16);

        $response = Http::post(env('API_BASE_URL') . '/api/login', [
            'username' => $username,
            'password' => $password,
        ]);

        if (!$response->ok()) {
            $error = $response->json();
            return redirect('/')->with('error', $error['reason'] ?? 'Social login failed');
        }

        $data = $response->json();
        if (!isset($data['token'])) {
            return redirect('/')->with('error', 'Invalid response from API');
        }

        $user = User::updateOrCreate(
            [$provider . '_id' => $providerId],
            [
                'name' => $username,
                'email' => $socialUser->getEmail() ?? $providerId . '@' . $provider . '.com',
                'password' => bcrypt($password),
            ]
        );

        Auth::login($user, true);

        if (!$user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
            return redirect()->route('verification.notice')->with('message', 'Please verify your email address to continue.');
        }

        return redirect('/game?token=' . $data['token']);
    }
}