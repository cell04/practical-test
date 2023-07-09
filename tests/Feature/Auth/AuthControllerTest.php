<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    /**
     * A basic feature test to retrieve token.
     */
    public function test_auth_get_token(): void
    {
        $user = User::factory()->create();

        $parameter = ['email' => $user->email, 'password' => 'password'];

    	$response = $this->postJson('/api/auth/login', $parameter);

        $response->assertOk();
    }

    /**
     * A basic feature test to register a user.
     */
    public function test_auth_register(): void
    {
        $parameter = [
        	'name' => 'sample name',
        	'email' => 'sample@sample.sample',
        	'password' => 'password',
        	'password_confirmation' => 'password'
        ];

    	$response = $this->postJson('/api/auth/register', $parameter);

        $response->assertOk()
        	->assertJson([
                'message' => 'Registration successful',
            ]);
    }

    /**
     * A basic feature test to retrieve authenticated user.
     */
    public function test_auth_get_user(): void
    {
        $user = User::factory()->create();

    	$response = $this->actingAs($user, 'api')
                         ->withSession(['banned' => false])
                         ->getJson('/api/auth/user');

        $response->assertOk();
    }

     /**
     * A basic feature test to retrieve authenticated user.
     */
    public function test_auth_change_user_password(): void
    {
        $user = User::factory()->create();

        $parameter = [
        	'user_id' => $user->id,
        	'current_password' => 'password',
        	'new_password' => '12345678',
        	'new_password_confirmation' => '12345678'
        ];

    	$response = $this->actingAs($user, 'api')
                         ->withSession(['banned' => false])
                         ->postJson('/api/auth/change-password', $parameter);

        $response->assertAccepted()
        	->assertJson([
                'message' => 'Changed password successful',
            ]);
    }
}
