<?php

namespace Tests\Integration;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{

    use RefreshDatabase; 
    
    public function testAUserCanBeCreated()
    {
        $this->assertEquals(1,1);
    }
}