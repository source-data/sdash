<?php

namespace Tests\Feature\API\Group;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Panel;
use App\Models\Group;
use Tests\TestCase;
use App\User;

class PublicGroupTest extends TestCase
{

  use RefreshDatabase;
  protected $user;
  protected $privatePanel;
  protected $publicPanel;
  protected $publicGroup;
  protected $privateGroup;

  public function setUp(): void
  {
    parent::setUp();
    $this->user = factory(User::class)->create(); //randomly generated user will have id = 0
    $this->privatePanel = factory(Panel::class)->create(['user_id' => $this->user->id, 'is_public' => false]);
    $this->publicPanel = factory(Panel::class)->create(['user_id' => $this->user->id, 'is_public' => true]);
    $this->privateGroup = factory(Group::class)->create([
      'user_id' => $this->user->id,
      'is_public' => false
    ]);
    $this->publicGroup = factory(Group::class)->create([
      'user_id' => $this->user->id,
      'is_public' => true
    ]);
    $this->privateGroup->panels()->attach([$this->privatePanel->id, $this->publicPanel->id]);
    $this->publicGroup->panels()->attach([$this->privatePanel->id, $this->publicPanel->id]);
  }

  public function testAnUnauthenticatedUserCanOnlySeePublicGroups()
  {
    $response = $this->get('/public-api/groups');

    // response will be successful
    $response->assertStatus(200);

    // the response data should contain one group
    $response->assertJsonCount(1, 'DATA');

    // the group ID should be the public group
    $response->assertJsonPath('DATA.0.id', $this->publicGroup->id);
  }

  public function testAnUnauthenticatedUserCanOnlySeePublicPanelInPublicGroups()
  {
    $response = $this->get('/public-api/groups');

    // there will only be one panel in the returned data
    $response->assertJsonCount(1, 'DATA.0.public_panels');

    // the panel ID will be the pubic panel's ID
    $response->assertJsonPath('DATA.0.public_panels.0.id', $this->publicPanel->id);

    // set public panel to private
    $this->publicPanel->is_public = false;
    $this->publicPanel->save();

    // make the request again
    $response = $this->get('/public-api/groups');

    // there will show no panels in the returned data
    $response->assertJsonCount(0, 'DATA.0.public_panels');
  }

  public function testAnUnauthenticatedUserCanRequestTheDetailsOfPublicGroupsPanelsByGroupId()
  {
    $response = $this->get("/public-api/groups/{$this->publicGroup->id}/panels");

    // response succeeds
    $response->assertStatus(200);

    // response contains one panel
    $response->assertJsonCount(1, 'DATA.data');

    // panel is the public panel
    $response->assertJsonPath('DATA.data.0.id', $this->publicPanel->id);
  }
}
