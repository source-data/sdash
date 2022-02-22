<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tag;
use App\Models\Panel;

class TagsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tags = DB::table('tags')->insert([
            [
                'content'    => 'Blots'
            ],
            [
                'content'    => 'Gels'
            ]
        ]);

        $smartTags = DB::table('tags')->insert([
            [
                'content'   => 'epoximycin'
            ],
            [
                'content'   => 'mice'
            ],
            [
                'content'   => 'p53'
            ],
            [
                'content'   => 'arteriolar'
            ],
        ]);

        Panel::whereIn('id', [1, 10, 35, 60, 65, 100, 134, 159])->each(function ($panel) use ($tags) {
            $panel->tags()->attach(1, ["origin" => "user"]);
            $panel->tags()->attach(3, ["origin" => "smarttag", "role" => "intervention", "type" => "geneprod"]);
            $panel->tags()->attach(4, ["origin" => "smarttag", "role" => "", "type" => "organism"]);
            $panel->tags()->attach(5, ["origin" => "smarttag", "role" => "assayed", "type" => "cell"]);
            $panel->tags()->attach(6, ["origin" => "smarttag", "category" => "disease"]);
        });
        Panel::whereIn('id', [1, 17, 18, 105, 81, 88, 40, 159])->each(function ($panel) use ($tags) {
            $panel->tags()->attach(2, ["origin" => "user"]);
        });
    }
}
