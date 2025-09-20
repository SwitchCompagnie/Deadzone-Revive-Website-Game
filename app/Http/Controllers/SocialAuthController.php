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
        $socialUser = Socialite::driver($provider)->user();
        $username = $this->generateUniqueUsername($socialUser, $provider);
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
            ['email' => $socialUser->getEmail() ?? $socialUser->getId() . '@' . $provider . '.com'],
            [
                'name' => $username,
                'password' => bcrypt($password),
                $provider . '_id' => $socialUser->getId(),
            ]
        );

        Auth::login($user, true);

        return redirect('/game?token=' . $data['token']);
    }

    private function generateUniqueUsername($socialUser, $provider)
    {
        $baseUsername = $socialUser->getNickname() ?? Str::slug($socialUser->getName() ?? 'user');
        $username = $baseUsername;
        $counter = 1;

        while (User::where('name', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        $badwords = ['dick'];
        if (in_array(strtolower($username), $badwords)) {
            $username = 'user' . Str::random(6);
        }

        $response = Http::get(env('API_BASE_URL') . '/api/userexist?username=' . urlencode($username));
        if ($response->ok() && $response->body() === 'yes') {
            $username = $baseUsername . $counter;
            $counter++;
            while ($response->ok() && $response->body() === 'yes') {
                $username = $baseUsername . $counter;
                $response = Http::get(env('API_BASE_URL') . '/api/userexist?username=' . urlencode($username));
                $counter++;
            }
        }

        return $username;
    }
}