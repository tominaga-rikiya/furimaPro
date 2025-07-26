<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $params = [
            [
                'user_id' => 1,
                'email_complete' => true,
                'email_message' => true,
            ],
            [
                'user_id' => 2,
                'email_complete' => false,
                'email_message' => true,
            ],
        ];

        foreach ($params as $param) {
            Setting::create($param);
        }
    }
}
