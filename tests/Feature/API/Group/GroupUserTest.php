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

  private function createGroup(Array $groupDetails, User $admin, User $member): Group
  {
    $group = factory(Group::class)->create($groupDetails);
    $group->users()->attach($admin->id, ['role' => 'admin', 'status' => 'confirmed']);
    $group->users()->attach($member->id, ['role' => 'user', 'status' => 'confirmed']);

    return $group;
  }

  public function setUp(): void
  {
    parent::setUp();
    $this->groupCreator = factory(User::class)->create(); //randomly generated user will have id = 0
    $this->groupMember = factory(User::class)->create(); //randomly generated user will have id = 1
    $this->privateGroup1 = $this->createGroup(['is_public' => false], $this->groupCreator, $this->groupMember);

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

    $this->groupWithCoverPhotoCreator = factory(User::class)->create();
    $this->groupWithCoverPhoto = $this->createGroup(['cover_photo' => 'cover_photo.jpg'], $this->groupWithCoverPhotoCreator, $this->groupMember);
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

  private function getGroupDetails(Group $group, User $actor)
  {
    return $this->actingAs($actor, 'sanctum')
                ->getJson('/api/groups/' . $group->id);
  }

  private function deleteCoverPhoto(Group $group, $actor)
  {
    $base = $this;
    if ($actor) {
        $base = $this->actingAs($actor, 'sanctum');
    }
    return $base->deleteJson('/api/groups/' . $group->id . '/cover');
  }

  private function assertCoverPhotoPresent(Group $group, String $message)
  {
    $groupDetails = $this->getGroupDetails($group, $group->administrators->first());
    $this->assertEquals($groupDetails['DATA']['cover_photo'], $group->cover_photo, $message);
  }

  private function assertNoCoverPhoto(Group $group, String $message)
  {
    $groupDetails = $this->getGroupDetails($group, $group->administrators->first());
    $this->assertEmpty($groupDetails['DATA']['cover_photo'], $message);
  }

  /**
   * @test
   *
   * @return void
   */
  public function a_group_admin_can_delete_the_cover_photo()
  {
    $group = $this->groupWithCoverPhoto;
    $user = $group->administrators->first();

    $this->assertCoverPhotoPresent($group, 'expected group to have a cover photo before deletion');
    $this->deleteCoverPhoto($group, $user)->assertStatus(200);
    $this->assertNoCoverPhoto($group, 'expected group to not have a cover photo after deletion');
  }

  /**
   * @test
   *
   * @return void
   */
  public function a_group_admin_can_delete_a_nonexistent_cover_photo()
  {
    $group = $this->privateGroup1;
    $user = $group->administrators->first();

    $this->assertNoCoverPhoto($group, 'expected group to not have a cover photo before deletion');
    $this->deleteCoverPhoto($group, $user)->assertStatus(200);
    $this->assertNoCoverPhoto($group, 'expected group to still not have a cover photo after deletion');
  }

  /**
   * @test
   *
   * @return void
   */
  public function a_group_member_cannot_delete_the_cover_photo()
  {
    $group = $this->groupWithCoverPhoto;
    $user = $group->confirmedUsers()->wherePivot('role', 'user')->first();

    $this->assertCoverPhotoPresent($group, 'expected group to have a cover photo before deletion');
    $this->deleteCoverPhoto($group, $user)->assertForbidden();
    $this->assertCoverPhotoPresent($group, 'expected group to still have a cover photo after deletion');
  }

  /**
   * @test
   *
   * @return void
   */
  public function an_unauthenticated_user_cannot_delete_the_cover_photo()
  {
    $group = $this->groupWithCoverPhoto;
    $user = null;

    $this->deleteCoverPhoto($group, $user)->assertUnauthorized();
    $this->assertCoverPhotoPresent($group, 'expected group to still have a cover photo after deletion');
  }

  /**
   * @test
   *
   * @return void
   */
  public function a_group_admin_cannot_delete_another_groups_cover_photo()
  {
    $group = $this->groupWithCoverPhoto;
    $user = $this->groupCreator;
    $this->assertNotContains($user, $group->administrators);

    $this->assertCoverPhotoPresent($group, 'expected group to have a cover photo before deletion');
    $this->deleteCoverPhoto($group, $user)->assertForbidden();
    $this->assertCoverPhotoPresent($group, 'expected group to still have a cover photo after deletion');
  }
}
