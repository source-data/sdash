<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AccessControlTest extends TestCase
{
    /**
     * A basic test that the app is working - can the user access the homepage.
     *
     * @return void
     */
    public function testCanAccessHomepage()
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
     * A logged out user should be able to see the about page.
     *
     * @return void
     */
    public function testUnauthenticatedUserCanAccessTheAboutPage()
    {
        $response = $this->get('/about');

        $this->assertGuest();

        $response->assertStatus(200);

        $response->assertSee('About');
    }
}
