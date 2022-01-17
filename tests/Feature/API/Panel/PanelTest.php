<?php

namespace Tests\Feature\API\Panel;

use App\User;
use Tests\TestCase;
use App\Models\Panel;
use App\Models\Group;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PanelTest extends TestCase
{

    use RefreshDatabase;
    protected $user;
    protected $panel;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create(); //randomly generated user will have id = 0
        $this->panel = factory(Panel::class)->create(['user_id' => $this->user->id]);
    }

    /**
     * @test
     *
     * @return void
     */
    public function a_logged_in_user_can_load_their_own_panel_details()
    {
        $response = $this->actingAs($this->user, 'sanctum')->getJson('api/panels/' . $this->panel->id);
        $response->assertStatus(200);
        $response->assertJson(['DATA' => [0 => ["user_id" => $this->user->id]]]);
    }

    /**
     * @test
     *
     * @return void
     */
    public function panel_details_include_owner_details()
    {
        $response = $this->actingAs($this->user, 'sanctum')->getJson('api/panels/' . $this->panel->id);
        $response->assertStatus(200);
        $response->assertJson(['DATA' => [0 => ["user" => ["email" => $this->user->email]]]]);
    }

    /**
     * @test
     *
     * @return void
     */
    public function when_a_user_uploads_a_panel_they_automatically_become_the_owner()
    {
        $response = $this->actingAs($this->user, 'sanctum')->post('api/panels', ['file' => UploadedFile::fake()->image('testfile.jpg')]);
        $response->assertStatus(200);
        $response->assertJson(['DATA' => ["user_id" => $this->user->id]]);
    }

    /**
     * @test
     *
     * @return void
     */
    public function when_a_user_uploads_a_panel_they_automatically_become_the_corresponding_author()
    {
        $response = $this->actingAs($this->user, 'sanctum')->post('api/panels', ['file' => UploadedFile::fake()->image('testfile.jpg')]);
        $response->assertStatus(200);
        $response->assertJson(['DATA' => ["authors" => [0 => ['id' => $this->user->id]]]]);
    }
}
