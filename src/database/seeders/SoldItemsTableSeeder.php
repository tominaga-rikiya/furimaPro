<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SoldItem;
use Carbon\Carbon;

class SoldItemsTableSeeder extends Seeder
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
                'item_id' => 2, 
                'sending_postcode' => '1500043',
                'sending_address' => '東京都渋谷区道玄坂1-12-1',
                'sending_building' => 'シブヤマークシティ',
                'is_completed' => true,
                'completed_at' => Carbon::now()->subDays(7),
                'created_at' => Carbon::now()->subDays(10),
            ],
            [
                'user_id' => 2,  
                'item_id' => 7,  
                'sending_postcode' => '1080014',
                'sending_address' => '東京都港区芝5丁目29-20610',
                'sending_building' => 'クロスオフィス三田',
                'is_completed' => false,
                'completed_at' => null,
                'created_at' => Carbon::now()->subDays(3),
            ],
        ];

        foreach ($params as $param) {
            SoldItem::create($param);
        }
    }
}
