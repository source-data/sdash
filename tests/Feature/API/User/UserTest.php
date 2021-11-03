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

    private function apiGet(String $path)
    {
        return $this->actingAs($this->user, 'sanctum')->getJson('/api' . $path);
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create(['role' => 'user']);
    }

    /**
     * @test
     *
     * @return void
     */
    public function an_authenticated_user_can_fetch_their_profile_data()
    {
        $response = $this->apiGet('/users/me');
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
}
