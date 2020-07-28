<?php

namespace Tests\Integration;

use App\User;
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
}
