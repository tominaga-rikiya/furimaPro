<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersTableSeeder extends Seeder
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
                'name' => '一般ユーザ1',
                'email' => 'general1@gmail.com',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('password'),
            ],
            [
                'name' => '一般ユーザ2',
                'email' => 'general2@gmail.com',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('password'),
            ],
            [
                'name' => '一般ユーザ3',
                'email' => 'general3@gmail.com',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($params as $param) {
            User::create($param);
        }
    }
}
