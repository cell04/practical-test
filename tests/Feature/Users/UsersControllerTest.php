<?php

namespace Tests\Feature\Users;

use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UsersControllerTest extends TestCase
{
    /**
     * A basic feature test to retrieve all users with pagination.
     */
    public function test_get_list_of_users_with_pagination(): void
    {
        $user = User::factory()->create();

    	$response = $this->actingAs($user, 'api')
                         ->withSession(['banned' => false])
                         ->getJson('/api/users');

        $response->assertOk();
    }

    /**
     * A basic feature test to retrieve all users without pagination.
     */
    public function test_get_list_of_users_without_pagination(): void
    {
        $user = User::factory()->create();

    	$response = $this->actingAs($user, 'api')
                         ->withSession(['banned' => false])
                         ->getJson('/api/users/all');

        $response->assertOk();
    }

    /**
     * A basic feature test to retrieve user details.
     */
    public function test_show_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')
                         ->withSession(['banned' => false])
                         ->getJson('/api/users/' . $user->id);

        $response->assertOk();
    }


    /**
     * A basic functional test to create a new user.
     */
    public function test_create_user(): void
    {
    	$user = User::factory()->create();

    	$formRequest = ['name' => 'Sally', 'email' => 'sample@sample.com', 'password' => '12345678', 'password_confirmation' => '12345678'];

		$response = $this->actingAs($user,'api')
					->postJson('/api/users', $formRequest);
 		
        $response->assertCreated()
            ->assertJson([
                'message' => 'Resource successfully stored',
            ]);
    }

    /**
     * A basic functional test to update the user.
     */
    public function test_update_user(): void
    {
    	$user = User::factory()->create();
    	$updateValue = User::factory()->create();

		$response = $this->actingAs($user,'api')
					->patchJson('/api/users/' . $updateValue->id, ['name' => 'sample']);

        $response->assertAccepted()
            ->assertJson([
                'message' => 'Resource successfully updated',
            ]);
    }

    /**
     * A basic functional test to delete the user.
     */
    public function test_delete_user(): void
    {
    	$user = User::factory()->create();
    	$updateValue = User::factory()->create();

		$response = $this->actingAs($user,'api')
					->deleteJson('/api/users/' . $updateValue->id);

        $response->assertAccepted()
            ->assertJson([
                'message' => 'Resource successfully deleted',
            ]);
    }

    /**
     * A basic functional test to following a user.
     */
    public function test_following_user(): void
    {
        $user = User::factory()->create();
        $following = User::factory()->create();

        $formRequest = ['following_id' => $following->id];

        $response = $this->actingAs($user,'api')
                    ->postJson('/api/users/' . $user->id . '/following', $formRequest);

        $response->assertAccepted()
            ->assertJson([
                'message' => 'Resource successfully followed',
            ]);
    }

    /**
     * A basic functional test to unfollow a user.
     */
    public function test_unfollow_user(): void
    {
        $user = User::factory()->create();
        $following = User::factory()->create();

        $formRequest = ['following_id' => $following->id];

        $response = $this->actingAs($user,'api')
                    ->postJson('/api/users/' . $user->id . '/unfollow', $formRequest);

        $response->assertAccepted()
            ->assertJson([
                'message' => 'Resource successfully unfollowed',
            ]);
    }

    /**
     * A basic feature test to retrieve all following users.
     */
    public function test_get_list_of_following_users(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')
                         ->withSession(['banned' => false])
                         ->getJson('/api/users/' . $user->id . '/following');

        $response->assertOk()
            ->assertJson([
                'message' => 'Resource successfully retrieve'
            ]);
    }


    /**
     * A basic feature test to retrieve users followers.
     */
    public function test_get_list_of_user_followers(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')
                         ->withSession(['banned' => false])
                         ->getJson('/api/users/' . $user->id . '/followers');

        $response->assertOk()
            ->assertJson([
                'message' => 'Resource successfully retrieve'
            ]);
    }

    /**
     * A basic feature test to retrieve all suggested users to follow.
     */
    public function test_get_list_of_suggested_users_to_follow(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')
                         ->withSession(['banned' => false])
                         ->getJson('/api/users/suggested-following');

        $response->assertOk()
            ->assertJson([
                'message' => 'Resource successfully retrieve'
            ]);
    }
}
