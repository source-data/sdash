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
    private $externalAuthorData;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->externalAuthorData = [
            'firstname'         => 'Pasta',
            'surname'           => 'Sauce',
            'email'             => 'pasta.sauce@spaghetti.net',
            'institution_name'  => 'University of Göttingen',
            'department_name'   => 'School of Excellence',
            'orcid'             => '0000-0002-6470-0634',
        ];
        $this->externalAuthor = ExternalAuthor::create($this->externalAuthorData);

        $this->panel = factory(Panel::class)->create(['user_id' => $this->user->id]);
        $this->panel->authors()->attach($this->user, ['order' => 0, 'role' => User::PANEL_ROLE_CORRESPONDING_AUTHOR]);
        $this->panel->externalAuthors()->attach($this->externalAuthor, ['order' => 1, 'role' => User::PANEL_ROLE_AUTHOR]);
    }

    public function testPanelDataContainsExternalAuthorDetails()
    {
        $response = $this->actingAs($this->user, 'sanctum')->getJson('api/panels/' . $this->panel->id);

        $response->assertStatus(200);

        $response->assertJson(["DATA" => [0 => ["external_authors" => [0 => ["email" => $this->externalAuthor->email]]]]]);
        $response->assertJson(["DATA" => [0 => ["external_authors" => [0 => ["orcid" => $this->externalAuthor->orcid]]]]]);
    }

    public function testPanelOwnerCanModifyExternalAuthors()
    {
        $currentUser = [
            'id'            => $this->user->id,
            'order'         => 0,
            'origin'        => 'users',
            'author_role'   => User::PANEL_ROLE_CORRESPONDING_AUTHOR,
            'email'         => $this->user->email
        ];

        $extraExternalAuthor = [
            'origin'            => 'external',
            'author_role'       => 'author',
            'email'             => 'xkk4slajd@dsjf.eom',
            'firstname'         => 'Stepton',
            'surname'           => 'Pantalonne',
            'institution_name'  => 'Paris Sorbonne',
            'department_name'   => 'Department of Geology',
            'orcid'             => '1dk02-2dojs-219djasl-2',
            'order'             => 1
        ];

        $response = $this->actingAs($this->user, 'sanctum')->putJson('api/panels/' . $this->panel->id . '/authors', ['authors' => [$currentUser, $extraExternalAuthor]]);

        $response->assertStatus(200);

        $response->assertJson(["DATA" => ["external_authors" => [0 => ["email" => $extraExternalAuthor["email"]]]]]);
        $response->assertJson(["DATA" => ["external_authors" => [0 => ["orcid" => $extraExternalAuthor["orcid"]]]]]);
    }
}
