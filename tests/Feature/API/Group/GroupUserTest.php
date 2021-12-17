<?php

namespace Tests\Feature\API\Group;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Panel;
use App\Models\Group;
use Tests\TestCase;
use App\User;

class GroupUserTest extends TestCase
{

  use RefreshDatabase, WithFaker;
  protected $groupCreator;
  protected $groupMember;
  protected $privateGroup1;
  protected $privateGroup2Details;

  public function setUp(): void
  {
    parent::setUp();
    $this->groupCreator = factory(User::class)->create(); //randomly generated user will have id = 0
    $this->groupMember = factory(User::class)->create(); //randomly generated user will have id = 1
    $this->privateGroup1 = factory(Group::class)->create([
      'is_public' => false
    ]);
    $this->privateGroup2Details = [
      'name'          => $this->faker->sentence(2),
      'description'   => $this->faker->sentence(8),
      'is_public'     => false,
      'panels'        => [],
      'url'           => $this->faker->url(),
      'members'       => [
        [
          'id'    => $this->groupMember->id,
          'admin' => false
        ]
      ]
    ];
    $this->privateGroup1->users()->attach($this->groupCreator->id, ['role' => 'admin', 'status' => 'confirmed']);
    $this->privateGroup1->users()->attach($this->groupMember->id, ['role' => 'user', 'status' => 'confirmed']);
  }

  /**
   *  @test
   *
   *  @return void
   */
  public function an_authenticated_user_can_create_a_group()
  {

    $response = $this->actingAs($this->groupCreator, 'sanctum')->postJson('api/groups', $this->privateGroup2Details);
    $response->assertStatus(200);
    $response->assertJson(['DATA' => [
      'name'        => $this->privateGroup2Details['name'],
      'description' => $this->privateGroup2Details['description'],
      'url'         => $this->privateGroup2Details['url'],
      'is_public'         => $this->privateGroup2Details['is_public']
    ]]);
  }


  /**
   *  @test
   *
   *  @return void
   */
  public function the_last_remaining_admin_cannot_leave_a_group()
  {
    $response = $this->actingAs($this->groupCreator, 'sanctum')->putJson('api/groups/' . $this->privateGroup1->id, [
      'id' => $this->privateGroup1->id,
      'description' => $this->privateGroup1->description,
      'is_public'   => false,
      'name'        => $this->privateGroup1->name,
      'url'         => $this->privateGroup1->url,
      'panels'      => [],
      'members'     => [
        [
          'id'      => $this->groupCreator->id,
          'admin'   => false,
          'status'  => 'confirmed',
        ],
        [
          'id'      => $this->groupMember->id,
          'admin'   => false,
          'status'  => 'confirmed',
        ]
      ]
    ]);

    $response->assertForbidden();
  }

  /**
   *  @test
   *
   *  @return void
   */
  public function the_group_creator_can_swap_admin_roles_with_another_member()
  {
    $response = $this->actingAs($this->groupCreator, 'sanctum')->putJson('api/groups/' . $this->privateGroup1->id, [
      'id' => $this->privateGroup1->id,
      'description' => $this->privateGroup1->description,
      'is_public'   => false,
      'name'        => $this->privateGroup1->name,
      'url'         => $this->privateGroup1->url,
      'panels'      => [],
      'members'     => [
        [
          'id'      => $this->groupCreator->id,
          'admin'   => false,
          'status'  => 'confirmed',
        ],
        [
          'id'      => $this->groupMember->id,
          'admin'   => true,
          'status'  => 'confirmed',
        ]
      ]
    ]);

    $response->assertOk();
  }

  /**
   *  @test
   *
   *  @return void
   */
  public function after_leaving_their_admin_role_the_group_creator_can_no_longer_edit_the_group()
  {
    $response = $this->actingAs($this->groupCreator, 'sanctum')->putJson('api/groups/' . $this->privateGroup1->id, [
      'id' => $this->privateGroup1->id,
      'description' => $this->privateGroup1->description,
      'is_public'   => false,
      'name'        => $this->privateGroup1->name,
      'url'         => $this->privateGroup1->url,
      'panels'      => [],
      'members'     => [
        [
          'id'      => $this->groupCreator->id,
          'admin'   => false,
          'status'  => 'confirmed',
        ],
        [
          'id'      => $this->groupMember->id,
          'admin'   => true,
          'status'  => 'confirmed',
        ]
      ]
    ]);

    $response->assertOk();

    $response = $this->actingAs($this->groupCreator, 'sanctum')->putJson('api/groups/' . $this->privateGroup1->id, [
      'id' => $this->privateGroup1->id,
      'description' => $this->faker->sentence(4),
      'is_public'   => false,
      'name'        => $this->privateGroup1->name,
      'url'         => $this->privateGroup1->url,
      'panels'      => [],
      'members'     => [
        [
          'id'      => $this->groupCreator->id,
          'admin'   => false,
          'status'  => 'confirmed',
        ],
        [
          'id'      => $this->groupMember->id,
          'admin'   => true,
          'status'  => 'confirmed',
        ]
      ]
    ]);

    $response->assertForbidden();
  }
}
