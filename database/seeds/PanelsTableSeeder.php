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
        factory(App\Models\Panel::class, 40)->create(["user_id" => 1])->each(function ($panel) {
            factory(App\Models\Comment::class, 6)->create(['panel_id' => $panel->id]);
            $panel->authors()->attach(1, ['role' => User::PANEL_ROLE_CURATOR, 'order' => 1]);
            $panel->authors()->attach(2, ['role' => User::PANEL_ROLE_AUTHOR, 'order' => 2]);
            $panel->authors()->attach(3, ['role' => User::PANEL_ROLE_CORRESPONDING_AUTHOR, 'order' => 3]);
        });

        //create user 2 and 3 panels
        factory(App\Models\Panel::class, 40)->create(['user_id' => 2])->each(function ($panel) {
            $panel->authors()->attach(2, ['role' => User::PANEL_ROLE_CORRESPONDING_AUTHOR, 'order' => 1]);
            factory(App\Models\Comment::class, 6)->create(['panel_id' => $panel->id]);
        });
        factory(App\Models\Panel::class, 40)->create(['user_id' => 3])->each(function ($panel) {
            $panel->authors()->attach(2, ['role' => User::PANEL_ROLE_CORRESPONDING_AUTHOR, 'order' => 1]);
            factory(App\Models\Comment::class, 6)->create(['panel_id' => $panel->id]);
        });

        //create public panels
        factory(App\Models\Panel::class, 5)->create(["is_public" => true])->each(function ($panel) {
            factory(App\Models\Comment::class, 6)->create(['panel_id' => $panel->id]);
        });
    }
}
