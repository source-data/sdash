<?php

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
        $panels = Panel::whereIn('user_id', [2, 3])->where('is_public', false)->limit(15)->get();

        $newGroups->each(function ($group) use ($panels, $users) {

            $users->each(function ($user) use ($group) {
                $group->users()->attach($user->id, ['status' => 'confirmed']);
            });

            $panels->each(function ($panel) use ($group) {
                $group->panels()->attach($panel->id);
            });
        });
    }
}
