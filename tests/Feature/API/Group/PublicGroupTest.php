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
      'is_public' => false
    ]);
    $this->publicGroup = factory(Group::class)->create([
      'is_public' => true
    ]);
    $this->publicGroup->users()->attach($this->user->id, ['role' => 'admin', 'status' => 'confirmed']);
    $this->privateGroup->panels()->attach([$this->privatePanel->id, $this->publicPanel->id]);
    $this->publicGroup->panels()->attach([$this->privatePanel->id, $this->publicPanel->id]);
  }

  /**
   * @test
   *
   * @return void
   */
  public function an_unauthenticated_user_can_only_view_public_groups()
  {
    $response = $this->getJson('/api/public/groups');

    // response will be successful
    $response->assertStatus(200);

    // the response data should contain one group
    $response->assertJsonCount(1, 'DATA');

    // the group ID should be the public group
    $response->assertJsonPath('DATA.0.id', $this->publicGroup->id);
  }

  /**
   * @test
   *
   * @return void
   */
  public function an_unauthenticated_user_can_only_see_public_panels_in_public_groups()
  {
    $response = $this->getJson('/api/public/groups');

    // there will only be one panel in the returned data
    $response->assertJsonCount(1, 'DATA.0.public_panels');

    // the panel ID will be the pubic panel's ID
    $response->assertJsonPath('DATA.0.public_panels.0.id', $this->publicPanel->id);

    // set public panel to private
    $this->publicPanel->is_public = false;
    $this->publicPanel->save();

    // make the request again
    $response = $this->getJson('/api/public/groups');

    // there will show no panels in the returned data
    $response->assertJsonCount(0, 'DATA.0.public_panels');
  }

  /**
   * @test
   *
   * @return void
   */
  public function an_unauthenticated_user_can_request_the_details_of_public_groups_using_the_group_id()
  {
    $response = $this->getJson("/api/public/groups/{$this->publicGroup->id}/panels");

    // response succeeds
    $response->assertStatus(200);

    // response contains one panel
    $response->assertJsonCount(1, 'DATA.data');

    // panel is the public panel
    $response->assertJsonPath('DATA.data.0.id', $this->publicPanel->id);
  }
}
