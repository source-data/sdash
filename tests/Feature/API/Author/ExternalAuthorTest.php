<?php

namespace Tests\Feature\API\Author;

use App\User;
use Tests\TestCase;
use App\Models\Panel;
use App\Models\ExternalAuthor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class ExternalAuthorTest extends TestCase
{

    use RefreshDatabase;

    private $user;
    private $panel;
    private $externalAuthor;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $externalAuthorData = [
            'firstname'         => 'Pasta',
            'surname'           => 'Sauce',
            'email'             => 'pasta.sauce@spaghetti.net',
            'institution_name'  => 'University of Göttingen',
            'department_name'   => 'School of Excellence',
            'orcid'             => '0000-0002-6470-0634',
        ];
        $this->externalAuthor = ExternalAuthor::create($externalAuthorData);

        $this->panel = factory(Panel::class)->create(['user_id' => $this->user->id]);
        $this->panel->authors()->attach($this->user, ['order' => 0, 'role' => User::PANEL_ROLE_CORRESPONDING_AUTHOR]);
        $this->panel->externalAuthors()->attach($this->externalAuthor, ['order' => 1, 'role' => User::PANEL_ROLE_AUTHOR]);
    }

    public function testPanelDataContainsExternalAuthorDetails()
    {
        $response = $this->actingAs($this->user, 'api')->get('api/panels/' . $this->panel->id);

        $response->assertStatus(200);

        $response->assertJson(["DATA" => [0 => ["external_authors" => [0 => ["email" => $this->externalAuthor->email]]]]]);
        $response->assertJson(["DATA" => [0 => ["external_authors" => [0 => ["orcid" => $this->externalAuthor->orcid]]]]]);
    }
}
