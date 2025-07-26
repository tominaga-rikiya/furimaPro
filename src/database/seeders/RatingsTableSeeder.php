<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rating;
use Carbon\Carbon;

class RatingsTableSeeder extends Seeder
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
                'sold_item_id' => 1, 
                'from_user_id' => 1,  
                'to_user_id' => 2,   
                'score' => 5,
                'comment' => '迅速な対応で商品も説明通りでした。また機会があればよろしくお願いします。',
                'created_at' => Carbon::now()->subDays(7),
            ],
        ];

        foreach ($params as $param) {
            Rating::create($param);
        }
    }
}
