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
     * A user can be set as the author of a panel
     *
     * @return void
     */
    public function testAUserCanBeSetAsAPanelCorrespondingAuthor()
    {
        $this->panel->authors()->attach($this->author->id, ['role' => User::PANEL_ROLE_CORRESPONDING_AUTHOR, 'order' => 0]);

        $authoredPanel = $this->author->authoredPanels()->first();

        $this->assertEquals($authoredPanel->id, $this->panel->id);

        $this->assertEquals($authoredPanel->role->role, User::PANEL_ROLE_CORRESPONDING_AUTHOR);
    }


    public function testAUserWhoIsCorrespondingAuthorOfAPanelCanAccessThePanelDetails()
    {
        $this->panel->authors()->attach($this->author->id, ['role' => User::PANEL_ROLE_CORRESPONDING_AUTHOR, 'order' => 0]);

        $response = $this->actingAs($this->author, 'sanctum')->getJson('api/panels/' . $this->panel->id);

        $response->assertStatus(200);

        $response->assertJson(["DATA" => [0 => ["id" => $this->panel->id]]]);
    }

    public function testAUserCannotAccessPanelDetailsOfAnotherUsersPanelIfTheyAreNotAnAuthor()
    {
        $response = $this->actingAs($this->author, 'sanctum')->getJson('api/panels/' . $this->panel->id);
        $response->assertStatus(401); //access denied
    }
}
