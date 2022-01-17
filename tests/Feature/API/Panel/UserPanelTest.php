<?php

namespace Tests\Feature\API\Panel;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\User;
use App\Models\Panel;

class UserPanelTest extends TestCase
{

    use RefreshDatabase;

    protected $user;
    protected $author;
    protected $panel;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create(['role' => 'user', 'id' => 2]);
        $this->author = factory(User::class)->create(['role' => 'user']);
        $this->panel = factory(Panel::class)->create(['user_id' => 2]);
    }

    /**
     * @test
     *
     * @return void
     */
    public function a_user_can_be_set_as_a_panel_corresponding_author()
    {
        $this->panel->authors()->attach($this->author->id, ['role' => User::PANEL_ROLE_CORRESPONDING_AUTHOR, 'order' => 0]);

        $authoredPanel = $this->author->authoredPanels()->first();

        $this->assertEquals($authoredPanel->id, $this->panel->id);

        $this->assertEquals($authoredPanel->role->role, User::PANEL_ROLE_CORRESPONDING_AUTHOR);
    }

    /**
     * @test
     *
     * @return void
     */
    public function a_user_who_is_a_corresponding_author_can_view_the_panel_details()
    {
        $this->panel->authors()->attach($this->author->id, ['role' => User::PANEL_ROLE_CORRESPONDING_AUTHOR, 'order' => 0]);

        $response = $this->actingAs($this->author, 'sanctum')->getJson('api/panels/' . $this->panel->id);

        $response->assertStatus(200);

        $response->assertJson(["DATA" => [0 => ["id" => $this->panel->id]]]);
    }

    /**
     * @test
     *
     * @return void
     */
    public function a_user_cannot_view_the_details_of_a_panel_where_they_are_not_an_author()
    {
        $response = $this->actingAs($this->author, 'sanctum')->getJson('api/panels/' . $this->panel->id);
        $response->assertStatus(401); //access denied
    }
}
