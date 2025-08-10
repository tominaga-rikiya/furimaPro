<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UsersTableSeeder::class,
            ProfilesTableSeeder::class,
            CategoriesTableSeeder::class,
            ConditionsTableSeeder::class,
            ItemsTableSeeder::class,
            CategoryItemsTableSeeder::class,
            CommentsTableSeeder::class,
            SoldItemsTableSeeder::class,
            MessagesTableSeeder::class,
        ]);
    }
}
