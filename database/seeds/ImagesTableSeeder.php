<?php

use Illuminate\Database\Seeder;
use Illuminate\Http\Testing\File;

use App\Models\Panel;
use App\Repositories\ImageRepository;
use App\Repositories\Interfaces\ImageRepositoryInterface;

/**
 * Adds an image of to each panel currently in the database.
 * 
 * Already existing images are overwritten. The dimensions and background color of the new image are randomized.
 */
class ImagesTableSeeder extends Seeder
{

    protected $imageRepository;

    public function __construct(ImageRepositoryInterface $imageRepository)
    {
        $this->imageRepository = $imageRepository;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // The possible x and y dimensions for the seeded images. Larger images may fail to be allocated.
        $dimensions = [20, 50, 100, 200, 400, 600, 800, 1000, 1600, 2400];
        $image_dimensions = [];
        foreach ($dimensions as $dim) {
            foreach ($dimensions as $other_dim) {
                $ratio = $dim / $other_dim;
                // we don't want any images with extreme height-to-width ratios. At most 200x1000 or 2000x400, but no
                // 2400x20 etc.
                $is_acceptable_ratio = 0.2 <= $ratio && $ratio <= 5;
                if ($is_acceptable_ratio) {
                    // Using only array_merge to add elements turns $image_dimensions into a set, i.e. no duplicates.
                    $image_dimensions = array_merge($image_dimensions, [[$dim, $other_dim]]);
                }
            }
        }

        $panels = Panel::all();
        foreach ($panels as $panel) {
            $image = new File($panel->title . '.jpg', $this->generateImage($image_dimensions));
            // Use the existing functionality to prevent code duplication for thumbnail generation etc.
            $this->imageRepository->storePanelImage($panel, $image);
        }
    }

    // basic functionality copied from Laravel's FileFactory but with added randomness for width & height and
    // background color.
    // https://github.com/laravel/framework/blob/7.x/src/Illuminate/Http/Testing/FileFactory.php
    protected function generateImage($dimensions) {
        [$width, $height] = $dimensions[array_rand($dimensions)];
        print $width . 'x' . $height . '\n';
        return tap(tmpfile(), function ($temp) use ($width, $height) {
            ob_start();

            $image = imagecreate($width, $height);
            $background_color = imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255));
            imagejpeg($image);

            fwrite($temp, ob_get_clean());
            imagedestroy($image);
        });
    }
}
