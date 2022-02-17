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

    private function deleteAvatarAsUser(User $target, $actor)
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

    private function assertAvatarPresent(User $user, String $message)
    {
        $userDetails = $this->getUserProfile($user);
        $this->assertEquals($userDetails['DATA']['avatar'], $user->avatar, $message);
    }
  
    private function assertNoAvatar(User $user, String $message)
    {
        $userDetails = $this->getUserProfile($user);
        $this->assertEmpty($userDetails['DATA']['avatar'], $message);
    }

    /**
     * @test
     *
     * @return void
     */
    public function an_authenticated_user_can_delete_their_avatar()
    {
        $user = $this->userWithAvatar;

        $this->assertAvatarPresent($user, 'expected user to have an avatar before deletion');
        $this->deleteAvatar($user)->assertStatus(200);
        $this->assertNoAvatar($user, 'expected user to not have an avatar after deletion');
    }

    /**
     * @test
     *
     * @return void
     */
    public function an_authenticated_user_can_delete_their_non_existent_avatar()
    {
        $user = $this->user;

        $this->assertNoAvatar($user, 'expected user to not have an avatar before deletion');
        $this->deleteAvatar($user)->assertStatus(200);
        $this->assertNoAvatar($user, 'expected user to still not have an avatar after deletion');
    }
    /**
     * @test
     *
     * @return void
     */
    public function an_unauthenticated_user_cannot_delete_an_avatar()
    {
        $user = $this->userWithAvatar;
        $actor = null;

        // no check that the avatar is present before deletion because there is apparently no good way to reset
        // authentication once it is set by the actingAs function. The only way to remain not-logged-in is to not log
        // in at all.
        $this->deleteAvatarAsUser($user, $actor)->assertUnauthorized();
        $this->assertAvatarPresent($user, 'expected user to still have an avatar after unauthorized deletion');
    }
    /**
     * @test
     *
     * @return void
     */
    public function an_authenticated_user_cannot_delete_another_users_avatar()
    {
        $user = $this->userWithAvatar;
        $actor = $this->user;

        $this->assertAvatarPresent($user, 'expected user to have an avatar before deletion');
        $this->deleteAvatarAsUser($user, $actor)->assertForbidden();
        $this->assertAvatarPresent($user, 'expected user to still have an avatar after forbidden deletion');
    }

    /**
     *  @test
     *
     *  @return void
     */
    public function a_user_cannot_get_another_users_data_using_the_user_id()
    {
        $response = $this->actingAs($this->user, 'sanctum')->getJson("/api/users/{$this->userWithAvatar->id}");
        $response->assertNotFound();
    }

    /**
     *  @test
     *
     *  @return void
     */
    public function a_user_can_get_another_users_data_using_the_user_slug()
    {
        $response = $this->actingAs($this->user, 'sanctum')->getJson("/api/users/{$this->userWithAvatar->user_slug}");
        $response->assertOK();
        $this->assertEquals($response['DATA']['id'], $this->userWithAvatar->id);
    }

    /**
     *  @test
     *
     *  @return void
     */
    public function a_guest_cannot_get_another_users_profile_data_with_the_user_id()
    {
        $response = $this->getJson("/api/users/{$this->userWithAvatar->id}");
        $response->assertUnauthorized();
    }

    /**
     *  @test
     *
     *  @return void
     */
    public function a_guest_cannot_get_another_users_profile_data_with_the_user_slug()
    {
        $response = $this->getJson("/api/users/{$this->userWithAvatar->user_slug}");
        $response->assertUnauthorized();
    }
}
