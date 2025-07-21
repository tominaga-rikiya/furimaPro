<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Profile;
use App\Models\User;
use App\Models\Item;

class ProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();
        $item = User::all();


        foreach ($users as $user) {
            Profile::create([
                'user_id' => $user->id,
                'profile_image' =>
                'storage/ArmaniMensClock.jpg',
                'postal_code' => '123-1234',
                'address' => 'toyama',
                'building_name' => 'ペイサージュ',
            ]);
        }
    }
}
