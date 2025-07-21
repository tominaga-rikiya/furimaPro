<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('categories')->insert([
            [
                'category' => json_encode(['name' => '時計']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category' => json_encode(['name' => '家電']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category' => json_encode(['name' => 'キッチン']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category' => json_encode(['name' => '衣類']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category' => json_encode(['name' => '食品']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
