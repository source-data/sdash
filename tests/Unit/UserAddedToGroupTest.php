<?php

namespace Tests\Unit;

use App\User;
use Mockery;
use App\Models\Panel;
use App\Models\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Str;
use App\Http\Controllers\API\GroupController;

class UserAddedToGroupTest extends TestCase
{
    use RefreshDatabase;
    private $invitingUser;
    private $invitedUser;
    private $group;
    private $token;

    public function setup(): void
    {
        parent::setUp();
        $this->invitingUser = factory(User::class)->create();
        $this->invitedUser = factory(User::class)->create();
        $this->group = factory(Group::class)->create(['user_id' => $this->invitingUser->id]);
        $this->token = sha1(now()->timestamp . $this->invitedUser->id . Str::random(24));
        // add the group creator as an admin
        $this->group->users()->attach($this->invitingUser->id, ['role' => 'admin', 'status' => 'confirmed']);
        // add the invited user as a pending invitation
        $this->group->users()->attach($this->invitedUser->id, ['role' => 'user', 'token' => $this->token]);
    }

    /**
     * @test
     *
     * @return void
     */
    public function group_controller_join_method_confirms_user_with_the_right_token()
    {
        $fakeGroupRepository = Mockery::mock('App\Repositories\Interfaces\GroupRepositoryInterface');
        $controller = new GroupController($fakeGroupRepository);
        $controller->join($this->group, $this->token);
        $groupFromDatabase = Group::find($this->group->id);
        $confirmedUsersFromDatabase = $groupFromDatabase->confirmedUsers()->get()->toArray();
        $invitedUser = $this->invitedUser;
        $newlyConfirmedUser = array_filter($confirmedUsersFromDatabase, function ($item) use ($invitedUser) {
            return $item['id'] === $invitedUser->id;
        });
        $this->assertEquals(1, count($newlyConfirmedUser));
        $this->assertEquals($this->invitedUser->email, array_shift($newlyConfirmedUser)['email']);
    }

    /**
     *  @test
     *
     *  @return void
     */
    public function group_controller_join_method_redirects_user_with_wrong_token()
    {
        $fakeGroupRepository = Mockery::mock('App\Repositories\Interfaces\GroupRepositoryInterface');
        $controller = new GroupController($fakeGroupRepository);
        $response = $controller->join($this->group, sha1(Str::random(24)));
        $this->assertEquals('302', $response->getStatusCode());
    }
}
