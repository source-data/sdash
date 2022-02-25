<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use \App\User;
use \App\Models\Panel;
use App\Models\Group;

class GroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //create user panels
        $newGroups = factory(Group::class, 4)->create();

        //get panels and users
        $users = User::all();
        $panels = Panel::where('is_public', false)->limit(8)->get();

        $newGroups->each(function ($group) use ($panels, $users) {

            $randomGroupAdmin = $users->random(1);

            $group->users()->attach($randomGroupAdmin[0]->id, ['status' => 'confirmed', 'role' => 'admin']);

            $users->each(function ($user) use ($group, $randomGroupAdmin) {
                if ($user->id !== $randomGroupAdmin[0]->id) $group->users()->attach($user->id, ['status' => 'confirmed']);
            });

            $panels->each(function ($panel) use ($group) {
                $group->panels()->attach($panel->id);
            });
        });
    }
}
