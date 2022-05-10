<?php

namespace Tests\Feature\API\Panel;

use App\Models\ExternalAuthor;
use App\User;
use Tests\TestCase;
use App\Models\Panel;
use App\Models\Group;
use App\Models\Image;
use App\Models\Tag;
use App\Repositories\ImageRepository;
use App\Repositories\FileRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mockery\MockInterface;

class PanelDuplicationTest extends TestCase
{

  use RefreshDatabase;

  protected $panelCreator;
  protected $panelAuthor;
  protected $panelExternalAuthor;
  protected $panel;
  protected $panelImage;
  protected $tags;

  public function setUp(): void
  {
    parent::setUp();
    // prevent the image repository from really creating the file system paths for the new panel
    $this->mock(FileRepository::class, function (MockInterface $mock) {
      $mock->allows('duplicatePanelFiles');
    });
    $this->panelCreator = factory(User::class)->create(['role' => User::USER_ROLE_USER]); //randomly generated user will have id = 0
    $this->panelAuthor = factory(User::class)->create(['role' => User::USER_ROLE_USER]);
    $this->panelExternalAuthor = factory(ExternalAuthor::class)->create();
    $this->panel = factory(Panel::class)->create(['user_id' => $this->panelCreator->id]);
    $this->panelImage = factory(Image::class)->create(['panel_id' => $this->panel->id]);
    $this->panel->authors()->attach($this->panelAuthor, ['role' => User::PANEL_ROLE_CORRESPONDING_AUTHOR, 'order' => 0]);
    $this->panel->externalAuthors()->attach($this->panelExternalAuthor, ['order' => 1]);

    DB::table('tags')->insert([
      [
        'content'    => 'Blots'
      ],
      [
        'content'    => 'Gels'
      ]
    ]);
    $this->tags = Tag::all();
  }

  /**
   * @test
   */
  public function a_panel_and_its_metadata_can_be_duplicated_to_a_panel_with_a_new_id()
  {

    $response = $this->actingAs($this->panelCreator, 'sanctum')->post('/api/panels/' . $this->panel->id . '/duplicate');

    $response->assertOk();

    // the new panel should have the same title
    $response->assertJsonPath('DATA.title', $this->panel->title);

    // the new panel should have an id
    $response->assertJsonStructure(['DATA' => ['id']]);

    //but it should not be the same as the source panel ID
    $this->assertNotEquals($this->panel->id, $response['DATA']['id'], 'The new panel ID should not be the same as the source panel ID.');
  }

  /**
   * @test
   */
  public function a_duplicated_panel_also_duplicates_the_tags()
  {
    $response = $this->actingAs($this->panelCreator, 'sanctum')->post('/api/panels/' . $this->panel->id . '/duplicate');

    $response->assertOk();

    // the new panel should have the same tags
    $response->assertJsonPath('DATA.tags', $this->panel->tags()->get()->toArray());
  }

  /**
   * @test
   */
  public function a_duplicated_panel_also_duplicates_the_authors()
  {
    $originalPanelAuthors = $this->panel->authors()->get()->toArray();
    // var_dump($originalPanelAuthors);
    $response = $this->actingAs($this->panelCreator, 'sanctum')->post('/api/panels/' . $this->panel->id . '/duplicate');

    $response->assertOk();

    // the panels should have the same number of authors
    $this->assertEquals(count($response['DATA']['authors']), count($originalPanelAuthors));

    // the authors should have the same details
    for ($i = 0; $i < count($originalPanelAuthors); $i++) {
      $this->assertEquals($response['DATA']['authors'][$i]['id'], $originalPanelAuthors[$i]['id']);
      $this->assertEquals($response['DATA']['authors'][$i]['user_slug'], $originalPanelAuthors[$i]['user_slug']);
      $this->assertEquals($response['DATA']['authors'][$i]['author_role']['role'], $originalPanelAuthors[$i]['author_role']['role']);
      $this->assertEquals($response['DATA']['authors'][$i]['author_role']['order'], $originalPanelAuthors[$i]['author_role']['order']);
    }
  }

  /**
   * @test
   */
  public function a_duplicated_panel_also_duplicates_the_external_authors()
  {
    $originalExternalAuthors = $this->panel->externalAuthors()->get()->toArray();
    // var_dump($originalExternalAuthors);
    $response = $this->actingAs($this->panelCreator, 'sanctum')->post('/api/panels/' . $this->panel->id . '/duplicate');

    $response->assertOk();

    // the panels should have the same number of authors
    $this->assertEquals(count($response['DATA']['external_authors']), count($originalExternalAuthors));

    // the authors should have the same details
    for ($i = 0; $i < count($originalExternalAuthors); $i++) {
      $this->assertEquals($response['DATA']['external_authors'][$i]['email'], $originalExternalAuthors[$i]['email']);
      $this->assertEquals($response['DATA']['external_authors'][$i]['author_role']['role'], $originalExternalAuthors[$i]['author_role']['role']);
      $this->assertEquals($response['DATA']['external_authors'][$i]['author_role']['order'], $originalExternalAuthors[$i]['author_role']['order']);
    }
  }

  /**
   * @test
   */
  public function a_user_with_no_modify_privileges_cannot_duplicate_a_panel()
  {
    $unprivilegedUser = factory(User::class)->create(['role' => User::USER_ROLE_USER]);
    $response = $this->actingAs($unprivilegedUser, 'sanctum')->post('/api/panels/' . $this->panel->id . '/duplicate');
    $response->assertUnauthorized();
  }

  /**
   *  @test
   *
   *  @return void
   */
  public function a_user_who_is_a_regular_author_cannot_duplicate_a_panel()
  {
    $regularAuthor = factory(User::class)->create(['role' => User::USER_ROLE_USER]);
    $this->panel->authors()->attach($regularAuthor, ['role' => User::PANEL_ROLE_AUTHOR, 'order' => 2]);
    $response = $this->actingAs($regularAuthor, 'sanctum')->post('/api/panels/' . $this->panel->id . '/duplicate');
    $response->assertUnauthorized();
  }
}
