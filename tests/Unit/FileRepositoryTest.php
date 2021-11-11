<?php

namespace Tests\Unit;

use App\User;
use App\Models\Panel;
use App\Models\File;
use Illuminate\Http\UploadedFile;
use App\Repositories\FileRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FileRepositoryTest extends TestCase
{

  use RefreshDatabase;

  /**
   *  @test
   *
   *  @return void
   */
  public function file_repository_accepts_a_panel_and_a_file_and_returns_the_correct_file_model()
  {
    $file = UploadedFile::fake()->image('test_image.jpg');
    $user = factory(User::class)->create([]);
    $panel = factory(Panel::class)->create(['user_id' => $user->id]);
    $repo = new FileRepository();
    $result = $repo->storePanelFile($panel, $file);
    // the repository returns an item of the file class
    $this->assertEquals(get_class($result), File::class);
    $fileInDb = File::first();
    $this->assertEquals($fileInDb->id, $result->id);
  }

  /**
   *  @test
   *
   *  @return void
   */
  public function file_respository_will_move_a_deleted_file_into_an_archive()
  {
    $upload = UploadedFile::fake()->image('test_image.jpg');
    $user = factory(User::class)->create([]);
    $panel = factory(Panel::class)->create(['user_id' => $user->id]);
    $repo = new FileRepository();
    $file = $repo->storePanelFile($panel, $upload);
    Storage::shouldReceive('move')->once()->with(
      $panel->save_path . DIRECTORY_SEPARATOR . 'attachments' . DIRECTORY_SEPARATOR . $file->filename,
      $panel->save_path . DIRECTORY_SEPARATOR . 'attachments' . DIRECTORY_SEPARATOR . 'archived' . DIRECTORY_SEPARATOR . $file->filename
    );
    $repo->archiveAndRemove($file);
  }
}
