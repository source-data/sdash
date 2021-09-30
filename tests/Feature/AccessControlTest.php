<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\User;

class AccessControlTest extends TestCase
{

    use RefreshDatabase;

    /**
     * A basic test that the app is working - can the user access the homepage.
     *
     * @return void
     */
    public function testAnyoneCanAccessHomepage()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /**
     * A logged out user should be redirected to the login page if they try to access the dashboard.
     *
     * @return void
     */
    public function testUnauthenticatedUserCannotAccessDashboard()
    {
        $response = $this->get('/dashboard');

        $this->assertGuest();

        $response->assertStatus(302);

        $response->assertLocation('/login');
    }
    /**
     * A logged out user should be redirected to the login page if they try to access their profile.
     *
     * @return void
     */
    public function testUnauthenticatedUserCannotAccessTheirOwnProfile()
    {
        $response = $this->get('/dashboard/user/me');

        $this->assertGuest();

        $response->assertStatus(302);

        $response->assertLocation('/login');
    }
    /**
     * A logged-in user should be able to access their dashboard
     *
     * @return void
     */
    public function testAuthenticatedUserCanAccessTheDashboard()
    {
        $user = factory(User::class)->create(['role' => 'user']);

        $response = $this->actingAs($user, 'api')->get('/dashboard');

        $response->assertStatus(200);
    }
}
