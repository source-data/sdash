<?php

use Illuminate\Database\Seeder;
use App\User;

class PanelsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //create user 1 panels
        factory(App\Models\Panel::class, 40)->create(["user_id" => 1])->each(function($panel) {
            factory(App\Models\Comment::class, 6)->create(['panel_id'=>$panel->id]);
            $panel->authors()->attach(1, ['role'=> User::PANEL_ROLE_CURATOR]);
            $panel->authors()->attach(2, ['role'=> User::PANEL_ROLE_AUTHOR]);
            $panel->authors()->attach(3, ['role'=> User::PANEL_ROLE_CORRESPONDING_AUTHOR]);
        });

        //create user 2 and 3 panels
        factory(App\Models\Panel::class, 80)->create()->each(function($panel) {
            factory(App\Models\Comment::class, 6)->create(['panel_id'=>$panel->id]);
        });

        //create public panels
        factory(App\Models\Panel::class, 40)->create(["made_public_at" => now()])->each(function($panel) {
            factory(App\Models\Comment::class, 6)->create(['panel_id'=>$panel->id]);
        });

    }
}
