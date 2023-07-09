<?php

namespace Tests\Feature\Tweets;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class TweetsControllerTest extends TestCase
{
    /**
     * A basic feature test to retrieve all tweets with pagination.
     */
    public function test_get_list_of_tweets_with_pagination(): void
    {
        $user = User::factory()->create();

    	$response = $this->actingAs($user, 'api')
                         ->withSession(['banned' => false])
                         ->getJson('/api/tweets');

        $response->assertOk();
    }

    /**
     * A basic feature test to retrieve all followed tweets with pagination.
     */
    public function test_get_list_of_followed_tweets_with_pagination(): void
    {
        $user = User::factory()->create();

    	$response = $this->actingAs($user, 'api')
                         ->withSession(['banned' => false])
                         ->getJson('/api/tweets/users-followed-tweets');

        $response->assertOk();
    }

    /**
     * A basic functional test to create a new tweet.
     */
    public function test_create_tweet(): void
    {
    	$user = User::factory()->create();

    	$formRequest = ['tweet' => 'Example tweet', 'image' => UploadedFile::fake()->image('avatar.jpg')];

		$response = $this->actingAs($user,'api')
					->postJson('/api/tweets', $formRequest);
 		
        $response->assertCreated()
            ->assertJson([
                'message' => 'Resource successfully stored',
            ]);
    }

    /**
     * A basic feature test to retrieve tweet details.
     */
    public function test_show_tweet(): void
    {
        $user = User::factory()->create();
        $tweet = $user->tweets()->create(['tweet' => 'Sample Tweet']);

        $response = $this->actingAs($user, 'api')
                         ->withSession(['banned' => false])
                         ->getJson('/api/tweets/' . $tweet->id);

        $response->assertOk();
    }

    /**
     * A basic functional test to update the tweet.
     */
    public function test_update_tweet(): void
    {
    	$user = User::factory()->create();
        $tweet = $user->tweets()->create(['tweet' => 'Sample Tweet']);

		$response = $this->actingAs($user,'api')
					->patchJson('/api/tweets/' . $tweet->id, ['tweet' => 'Update Tweet Value']);

        $response->assertAccepted()
            ->assertJson([
                'message' => 'Resource successfully updated',
            ]);
    }

    /**
     * A basic functional test to delete the tweet.
     */
    public function test_delete_tweet(): void
    {
    	$user = User::factory()->create();
    	$tweet = $user->tweets()->create(['tweet' => 'Sample Tweet']);

		$response = $this->actingAs($user,'api')
					->deleteJson('/api/tweets/' . $tweet->id);

        $response->assertAccepted()
            ->assertJson([
                'message' => 'Resource successfully deleted',
            ]);
    }

    /**
     * A basic functional test to add image on the tweet.
     */
    public function test_add_image_on_the_tweet(): void
    {
    	$user = User::factory()->create();
    	$tweet = $user->tweets()->create(['tweet' => 'Sample Tweet']);

    	$formRequest = ['image' => UploadedFile::fake()->image('avatar.jpg')];

		$response = $this->actingAs($user,'api')
					->postJson('/api/tweets/' . $tweet->id . '/images', $formRequest);

        $response->assertCreated()
            ->assertJson([
                'message' => 'Resource successfully stored',
            ]);
    }

    /**
     * A basic functional test to remove image on the tweet.
     */
    public function test_remove_image_on_the_tweet(): void
    {
    	$user = User::factory()->create();
    	$tweet = $user->tweets()->create(['tweet' => 'Sample Tweet']);
    	$image = $tweet->images()->create(['file_path' => '/sample/images/path.jpg']);

		$response = $this->actingAs($user,'api')
					->deleteJson('/api/tweets/' . $tweet->id . '/images/' . $image->id);

        $response->assertAccepted()
            ->assertJson([
                'message' => 'Resource successfully deleted',
            ]);
    }
}
