<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'ali@karbaan.test',
        ]);

        $this->post('/login', [
            'email' => 'ali@karbaan.test',
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_invalid_password(): void
    {
        User::factory()->create([
            'email' => 'ali@karbaan.test',
        ]);

        $this->from(route('login'))
            ->post('/login', [
                'email' => 'ali@karbaan.test',
                'password' => 'wrong-password',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_inactive_user_cannot_login(): void
    {
        User::factory()->inactive()->create([
            'email' => 'inactive@karbaan.test',
        ]);

        $this->from(route('login'))
            ->post('/login', [
                'email' => 'inactive@karbaan.test',
                'password' => 'password',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHas('error');

        $this->assertGuest();
    }
}
