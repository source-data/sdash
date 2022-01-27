<?php

namespace Tests\Feature\API\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\User;

class UserTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $userWithAvatar;

    private function getUserProfile(User $user)
    {
        return $this->actingAs($user, 'sanctum')->getJson('/api/users/me');
    }

    private function deleteAvatarAsUser(User $target, User $actor)
    {
        $base = $this;
        if ($actor) {
            $base = $this->actingAs($actor, 'sanctum');
        }
        return $base->deleteJson('/api/users/' . $target->user_slug . '/avatar');
    }
    private function deleteAvatar(User $user)
    {
        return $this->deleteAvatarAsUser($user, $user);
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create(['role' => 'user']);

        $this->userWithAvatar = factory(User::class)->create([
            'role' => 'user',
            'avatar' => 'avatar.jpg',
        ]);
    }

    /**
     * @test
     *
     * @return void
     */
    public function an_authenticated_user_can_fetch_their_profile_data()
    {
        $response = $this->getUserProfile($this->user);
        $response->assertStatus(200);
        $this->assertEquals($response['DATA']['id'], $this->user->id);
        $this->assertEquals($response['DATA']['role'], $this->user->role);
        $this->assertEquals($response['DATA']['email'], $this->user->email);
    }

    /**
     * @test
     *
     * @return void
     */
    public function an_unauthenticated_user_cannot_access_the_profile_endpoint()
    {
        $response = $this->getJson('/api/users/me');
        $response->assertUnauthorized();
    }

    public function an_authenticated_user_can_delete_their_avatar()
    {
        $userWithAvatar = $this->userWithAvatar;
        $responseProfileBeforeDelete = $this->getUserProfile($userWithAvatar);
        $this->assertEquals($responseProfileBeforeDelete['DATA']['avatar'], $userWithAvatar->avatar);

        $responseDeleteAvatar = $this->deleteAvatar($userWithAvatar);
        $responseDeleteAvatar->assertStatus(200);

        $responseProfileAfterDelete = $this->getUserProfile($userWithAvatar);
        $this->assertEmpty($responseProfileAfterDelete['DATA']['avatar'], 'expected user to not have an avatar after deletion');
    }

    /**
     * @test
     *
     * @return void
     */
    public function an_authenticated_user_can_delete_their_non_existent_avatar()
    {
        $userWithoutAvatar = $this->user;
        $responseProfileBeforeDelete = $this->getUserProfile($userWithoutAvatar);
        $this->assertEmpty($responseProfileBeforeDelete['DATA']['avatar'], 'expected user to not have an avatar before deletion');

        $responseDeleteAvatar = $this->deleteAvatar($userWithoutAvatar);
        $responseDeleteAvatar->assertStatus(200);

        $responseProfileAfterDelete = $this->getUserProfile($userWithoutAvatar);
        $this->assertEmpty($responseProfileAfterDelete['DATA']['avatar'], 'expected user to still not have an avatar after deletion');
    }
    /**
     * @test
     *
     * @return void
     */
    public function an_unauthenticated_user_cannot_delete_an_avatar()
    {
        $responseDeleteAvatar = $this->deleteJson('/api/users/' . $this->userWithAvatar->id . '/avatar');
        $responseDeleteAvatar->assertUnauthorized();
    }
    /**
     * @test
     *
     * @return void
     */
    public function an_authenticated_user_cannot_delete_another_users_avatar()
    {
        $responseDeleteAvatar = $this->deleteAvatarAsUser($this->userWithAvatar, $this->user);
        $responseDeleteAvatar->assertForbidden();
    }
}
