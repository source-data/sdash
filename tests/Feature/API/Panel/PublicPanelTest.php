<?php

namespace Tests\Feature\API\Panel;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Panel;
use App\User;
use Tests\TestCase;

class PublicPanelTest extends TestCase
{

    use RefreshDatabase;
    protected $user;
    protected $privatePanel;
    protected $publicPanel;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create(); //randomly generated user will have id = 0
        $this->privatePanel = factory(Panel::class)->create(['user_id' => $this->user->id, 'is_public' => false]);
        $this->publicPanel = factory(Panel::class)->create(['user_id' => $this->user->id, 'is_public' => true]);
    }

    public function testThePublicPanelListWillOnlyContainPublicPanels()
    {
        $response = $this->getJson('api/public/panels');

        $response->assertStatus(200);

        //the DATA array will have one item
        $response->assertJsonCount(1, 'DATA.data');

        //the one item will have the public panel id
        $response->assertJsonPath('DATA.data.0.id', $this->publicPanel->id);
    }

    public function testAnUnauthenticatedUserCanLoadPublicPanelDetail()
    {
        $response = $this->getJson("api/public/panels/{$this->publicPanel->id}");

        $response->assertStatus(200);

        //the response will contain the correct id
        $response->assertJsonPath('DATA.0.id', $this->publicPanel->id);
    }

    public function testAnUnauthenticatedUserCannotLoadPrivatePanelDetails()
    {
        $response = $this->getJson("api/public/panels/{$this->privatePanel->id}");

        $response->assertStatus(401);
    }
}
