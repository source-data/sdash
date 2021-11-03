<?php

namespace Tests\Feature\API\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Carbon\Carbon;
use Tests\TestCase;
use App\User;

class UserEmailVerificationTest extends TestCase
{

    use RefreshDatabase;

    private $verifiedUser;
    private $unverifiedUser;
    private $tempUserData = [
        'firstname' => 'Bob',
        'surname' => 'Dobalina',
        'email' => 'bd@astrazeneca.se',
        'password' => 'blabbadabbadab',
        'password_confirmation' => 'blabbadabbadab',
        'orcid' => '0000-0002-3202-0263',
        'institution_name' => 'Abracadabra',
        'institution_address' => 'Beep beep beep beep',
        'department_name' => 'Zootopia',
        'linkedin' => '',
        'twitter' => '',
        'confirmation' => [
            true,
            true,
            true,
        ]
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->verifiedUser = factory(User::class)->create(['role' => 'user']);
        $this->unverifiedUser = factory(User::class)->create(['role' => 'user', 'email_verified_at' => null]);
    }


    /**
     * @test
     * @return void
     */
    public function fetching_a_verified_user_tells_us_they_have_a_verified_email_address()
    {
        $response = $this->actingAs($this->verifiedUser, 'sanctum')->getJson('/api/users/me');

        $response->assertStatus(200);
        $this->assertEquals(Carbon::parse($response['DATA']['email_verified_at']), $this->verifiedUser->email_verified_at);
    }

    /**
     * @test
     * @return void
     */
    public function a_user_with_an_unverified_email_address_cannot_fetch_their_profile_data()
    {
        $response = $this->actingAs($this->unverifiedUser, 'sanctum')->getJson('/api/users/me');

        $response->assertStatus(403);
    }

    /**
     * @test
     * @return void
     */
    public function registering_a_user_puts_them_in_the_unverified_state()
    {
        $response = $this->postJson('/api/users', $this->tempUserData);
        $response->assertStatus(201);

        $tempUser = User::where('email', $this->tempUserData['email'])->first();
        $response = $this->actingAs($tempUser, 'sanctum')->getJson('/api/users/me');
        $response->assertStatus(403);
    }
}
