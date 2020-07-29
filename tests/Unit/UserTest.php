<?php

namespace Tests\Unit;

use App\User;
use App\Models\Panel;
use App\Models\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{

    use RefreshDatabase;

    public function testAUserCanBeCreatedAndStoredInDatabase()
    {
        $user = factory(User::class)->create();

        $dbUser = User::first();

        $this->assertEquals($user->id, $dbUser->id);
        $this->assertEquals($user->email, $dbUser->email);
        $this->assertEquals($user->role, $dbUser->role);
    }

    public function testUserModelCanIdentifyWhetherItsAnAdmin()
    {
        $adminUser = factory(User::class)->create(['role' => 'admin']);
        $superUser = factory(User::class)->create(['role' => 'superadmin']);
        $normalUser = factory(User::class)->create(['role' => 'user']);

        $this->assertTrue($adminUser->is_admin());
        $this->assertTrue($superUser->is_superadmin());
        $this->assertFalse($normalUser->is_superadmin());
        $this->assertFalse($normalUser->is_admin());
    }

    public function testAUserCanOwnPanels()
    {
        $user = factory(User::class)->create();
        $panel1 = factory(Panel::class)->create(['user_id' => $user->id]);

        $userPanels = User::find($user->id)->panels;
        $this->assertEquals(count($userPanels), 1, "There should be one panel");
        $this->assertEquals($userPanels[0]->id, $panel1->id);

        $panel2 = factory(Panel::class)->create(['user_id' => $user->id]);

        $userPanels2 = User::find($user->id)->panels;
        $this->assertEquals(count($userPanels2), 2, "There should be two panels");
    }


    public function testAUserCanJoinAGroupAsAPendingUser()
    {
        $user = factory(User::class)->create();
        $owner = factory(User::class)->create(['id' => 99]);
        $group = factory(Group::class)->create(['user_id' => $owner->id]);
        $user->groups()->attach($group->id, ['status' => 'pending']);

        $dbUser = User::find($user->id);

        $groups = $dbUser->groups()->withPivot('status')->get();
        $this->assertEquals(count($groups), 1);
        $this->assertEquals($group->id, $groups[0]->id);
        $this->assertEquals('pending', $groups[0]->pivot->status);

        $pendingGroups = $dbUser->pendingGroups;
        $this->assertEquals(count($pendingGroups), 1);
        $this->assertEquals($pendingGroups[0]->id, $group->id);
    }

    public function testAUserCanJoinAGroupAsAConfirmedUser()
    {
        $user = factory(User::class)->create();
        $owner = factory(User::class)->create(['id' => 99]);
        $group = factory(Group::class)->create(['user_id' => $owner->id]);
        $user->groups()->attach($group->id, ['status' => 'confirmed']);

        $dbUser = User::find($user->id);

        $groups = $dbUser->groups()->withPivot('status')->get();
        $this->assertEquals(count($groups), 1);
        $this->assertEquals($group->id, $groups[0]->id);
        $this->assertEquals('confirmed', $groups[0]->pivot->status);

        $confirmedGroups = $dbUser->confirmedGroups;
        $this->assertEquals(count($confirmedGroups), 1);
        $this->assertEquals($confirmedGroups[0]->id, $group->id);
    }
}
