<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            [
                'user_id' => 1,
                'name' => '腕時計',
                'brand_name' => 'シルバースター',
                'price' => 15000,
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'image' => 'storage/ArmaniMensClock.jpg',
                'condition_id' => 1,
                'category_ids' => [1],  
                'is_sold' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'name' => 'HDD',
                'brand_name' => 'オリオン',
                'price' => 5000,
                'description' => '高速で信頼性の高いハードディスク',
                'image' => 'storage/HDDHaedDisk.jpg',
                'condition_id' => 2,
                'category_ids' => [2],  
                'is_sold' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'name' => '玉ねぎ3kg',
                'brand_name' => 'ライトウェーブ',
                'price' => 4000,
                'description' => '新鮮な玉ねぎ3kgのセット',
                'image' => 'storage/iLovelMGd.jpg',
                'condition_id' => 3,
                'category_ids' => [5],  
                'is_sold' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 4,
                'name' => '革靴',
                'brand_name' => 'レトロファイン',
                'price' => 4000,
                'description' => 'クラシックなデザインの革靴',
                'image' => 'storage/LeatherShoesProductPhoto.jpg',
                'condition_id' => 4,
                'category_ids' => [4],  
                'is_sold' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 5,
                'name' => 'マイク',
                'brand_name' => 'アクアリーフ',
                'price' => 3000,
                'description' => '高音質のレコーディング用マイク',
                'image' => 'storage/MusicMic4632231.jpg',
                'condition_id' => 1,
                'category_ids' => [2],  
                'is_sold' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 6,
                'name' => 'ショルダーバッグ',
                'brand_name' => 'ブリリアント・コレクション',
                'price' => 2500,
                'description' => 'おしゃれなショルダーバッグ',
                'image' => 'storage/Pursefashionpocket.jpg',
                'condition_id' => 2,
                'category_ids' => [4],  
                'is_sold' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 7,
                'name' => 'コーヒーミル',
                'brand_name' => 'スカイライン',
                'price' => 3000,
                'description' => '手動のコーヒーミル',
                'image' => 'storage/WaitresswithCoffeeGrinder.jpg',
                'condition_id' => 3,
                'category_ids' => [3, 5],  
                'is_sold' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 8,
                'name' => 'メイクセット',
                'brand_name' => 'ノーブルエッジ',
                'price' => 2500,
                'description' => '便利なメイクアップセット',
                'image' => 'storage/makeup.jpg',
                'condition_id' => 2,
                'category_ids' => [4],  
                'is_sold' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 9,
                'name' => 'ノートPC',
                'brand_name' => 'ダークブルーム',
                'price' => 45000,
                'description' => '高性能なノートパソコン',
                'image' => 'storage/LivingRoomLaptop.jpg',
                'condition_id' => 1,
                'category_ids' => [2],  
                'is_sold' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 10,
                'name' => 'タンブラー',
                'brand_name' => 'モダンエレメント',
                'price' => 500,
                'description' => '使いやすいタンブラー',
                'image' => 'storage/Tumblersouvenir.jpg',
                'condition_id' => 4,
                'category_ids' => [3],  
                'is_sold' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($items as $itemData) {
            Item::create($itemData);
        }
    }
}