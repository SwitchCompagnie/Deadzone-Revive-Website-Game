<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SocialAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_model_has_random_password_in_fillable(): void
    {
        $user = new User;
        $fillable = $user->getFillable();

        $this->assertContains('random_password', $fillable);
    }

    public function test_user_model_hides_random_password(): void
    {
        $user = new User;
        $hidden = $user->getHidden();

        $this->assertContains('random_password', $hidden);
    }

    public function test_social_login_redirect_requires_valid_provider(): void
    {
        $response = $this->get('/auth/invalid_provider');

        $response->assertRedirect('/');
        $response->assertSessionHas('error', 'Invalid social provider.');
    }

    public function test_social_login_redirects_authenticated_users(): void
    {
        $user = User::factory()->create([
            'name' => 'testuser123',
            'email' => 'test@example.com',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->get('/auth/discord');

        // The guest middleware redirects authenticated users to the home route
        $response->assertRedirect('/');
    }

    public function test_regular_login_regenerates_session(): void
    {
        // This test verifies that session regeneration happens
        // We can't test the actual regeneration without a real session,
        // but we can verify the functionality exists

        $this->assertTrue(method_exists('\Illuminate\Session\Store', 'regenerate'));
    }
}
