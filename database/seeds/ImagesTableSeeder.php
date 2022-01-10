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
        $dimensions = [100, 200, 400, 600, 800, 1000, 1600, 2400];
        $image_dimensions = [];
        foreach ($dimensions as $dim) {
            foreach ($dimensions as $other_dim) {
                $ratio = $dim / $other_dim;
                // we don't want any images with extreme height-to-width ratios. At most 200x1000 or 2000x400, but no
                // 2400x100 etc.
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
        return tap(tmpfile(), function ($temp) use ($width, $height) {
            ob_start();

            $image = imagecreate($width, $height);
            $background_color = imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255));

            $shapes_size = min($width, $height) / 2;
            $shapes_margin = $shapes_size / 5;
            $shapes_color = imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255));
            // draw a rectangle in the top left corner
            imagefilledrectangle(
                $image,
                $shapes_margin,                // x1
                $shapes_margin,                // y1
                $shapes_size + $shapes_margin, // x2
                $shapes_size + $shapes_margin, // y2
                $shapes_color
            );
            // draw a circle in the bottom right corner
            $circle_radius = $shapes_size / 2;
            imagefilledellipse(
                $image,
                $width - $shapes_margin - $circle_radius,  // center_x
                $height - $shapes_margin - $circle_radius, // center_y
                $shapes_size,                              // width
                $shapes_size,                              // height
                $shapes_color
            );
            imagejpeg($image);

            fwrite($temp, ob_get_clean());
            imagedestroy($image);
        });
    }
}
