<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CategoryItem;

class CategoryItemsTableSeeder extends Seeder
{
    public function run()
    {
        $params = [
            ['item_id' => 1, 'category_id' => 1],  
            ['item_id' => 1, 'category_id' => 5],  
            ['item_id' => 2, 'category_id' => 2], 
            ['item_id' => 3, 'category_id' => 10],
            ['item_id' => 4, 'category_id' => 1],  
            ['item_id' => 4, 'category_id' => 5],  
            ['item_id' => 5, 'category_id' => 2],  
            ['item_id' => 6, 'category_id' => 2],  
            ['item_id' => 7, 'category_id' => 1],  
            ['item_id' => 7, 'category_id' => 4],  
            ['item_id' => 8, 'category_id' => 3],  
            ['item_id' => 8, 'category_id' => 10], 
            ['item_id' => 9, 'category_id' => 3],  
            ['item_id' => 9, 'category_id' => 10], 
            ['item_id' => 10, 'category_id' => 6], 
            ['item_id' => 10, 'category_id' => 4], 
        ];

        foreach ($params as $param) {
            CategoryItem::create($param);
        }
    }
}
