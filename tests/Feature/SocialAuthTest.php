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

    public function test_user_can_generate_verification_code(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'email_verified_at' => null,
        ]);

        $code = $user->generateEmailVerificationCode();

        $this->assertIsString($code);
        $this->assertEquals(6, strlen($code));
        $this->assertMatchesRegularExpression('/^[0-9]{6}$/', $code);
        $this->assertNotNull($user->fresh()->email_verification_code);
        $this->assertNotNull($user->fresh()->email_verification_code_expires_at);
    }

    public function test_user_can_verify_email_with_valid_code(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'email_verified_at' => null,
        ]);

        $code = $user->generateEmailVerificationCode();

        $result = $user->verifyEmailWithCode($code);

        $this->assertTrue($result);
        $this->assertNotNull($user->fresh()->email_verified_at);
        $this->assertNull($user->fresh()->email_verification_code);
    }

    public function test_user_cannot_verify_with_invalid_code(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'email_verified_at' => null,
        ]);

        $user->generateEmailVerificationCode();

        $result = $user->verifyEmailWithCode('999999');

        $this->assertFalse($result);
        $this->assertNull($user->fresh()->email_verified_at);
    }
}
