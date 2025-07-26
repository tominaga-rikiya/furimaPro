<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\Like;

class ItemsTableSeeder extends Seeder
{
    public function run()
    {
        $params = [
            [
                'name' => '腕時計',
                'price' => 15000,
                'brand' => 'Rolax',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'img_url' => 'public/img/mens_clock.jpg',
                'user_id' => 2,
                'condition_id' => 1,
            ],
            [
                'name' => 'HDD',
                'price' => 5000,
                'brand' => '西芝',
                'description' => '高速で信頼性の高いハードディスク',
                'img_url' => 'public/img/hard_disk.jpg',
                'user_id' => 2,
                'condition_id' => 2,
            ],
            [
                'name' => '玉ねぎ3束',
                'price' => 300,
                'brand' => '',
                'description' => '新鮮な玉ねぎ3束のセット',
                'img_url' => 'public/img/onion.jpg',
                'user_id' => 2,
                'condition_id' => 3,
            ],
            [
                'name' => '革靴',
                'price' => 4000,
                'brand' => '',
                'description' => 'クラシックなデザインの革靴',
                'img_url' => 'public/img/leather_shoes.jpg',
                'user_id' => 2,
                'condition_id' => 4,
            ],
            [
                'name' => 'ノートPC',
                'price' => 45000,
                'brand' => '',
                'description' => '高性能なノートパソコン',
                'img_url' => 'public/img/laptop_PC.jpg',
                'user_id' => 2,
                'condition_id' => 1,
            ],
            [
                'name' => 'マイク',
                'price' => 8000,
                'brand' => '',
                'description' => '高音質のレコーディング用マイク',
                'img_url' => 'public/img/mic.jpg',
                'user_id' => 2,
                'condition_id' => 2,
            ],
            [
                'name' => 'ショルダーバッグ',
                'price' => 3500,
                'brand' => '',
                'description' => 'おしゃれなショルダーバッグ',
                'img_url' => 'public/img/shoulder_bag.jpg',
                'user_id' => 1,
                'condition_id' => 3,
            ],
            [
                'name' => 'タンブラー',
                'price' => 500,
                'brand' => '',
                'description' => '使いやすいタンブラー',
                'img_url' => 'public/img/tumbler.jpg',
                'user_id' => 1,
                'condition_id' => 4,
            ],
            [
                'name' => 'コーヒーミル',
                'price' => 4000,
                'brand' => 'Starbacks',
                'description' => '手動のコーヒーミル',
                'img_url' => 'public/img/coffer_mill.jpg',
                'user_id' => 1,
                'condition_id' => 1,
            ],
            [
                'name' => 'メイクセット',
                'price' => 2500,
                'brand' => '',
                'description' => '便利なメイクアップセット',
                'img_url' => 'public/img/make_set.jpg',
                'user_id' => 1,
                'condition_id' => 2,
            ],
            [
                'name' => 'スニーカー',
                'price' => 12000,
                'brand' => 'Nike',
                'description' => '人気のランニングシューズ',
                'img_url' => 'public/img/sneaker.jpg',
                'user_id' => 3,
                'condition_id' => 1,
            ],
            [
                'name' => '帽子',
                'price' => 2800,
                'brand' => '',
                'description' => 'おしゃれなキャップ',
                'img_url' => 'public/img/hat.jpg',
                'user_id' => 3,
                'condition_id' => 2,
            ],
            [
                'name' => 'メガネ',
                'price' => 8500,
                'brand' => 'Ray-Ban',
                'description' => 'クラシックなデザインのサングラス',
                'img_url' => 'public/img/glasses.jpg',
                'user_id' => 3,
                'condition_id' => 1,
            ],
            [
                'name' => 'テレビ',
                'price' => 35000,
                'brand' => 'Sony',
                'description' => '32インチ液晶テレビ',
                'img_url' => 'public/img/tv.jpg',
                'user_id' => 1,
                'condition_id' => 2,
            ],
            [
                'name' => '財布',
                'price' => 6500,
                'brand' => 'Coach',
                'description' => '本革の二つ折り財布',
                'img_url' => 'public/img/wallet.jpg',
                'user_id' => 2,
                'condition_id' => 3,
            ],
            [
                'name' => 'イヤリング',
                'price' => 3200,
                'brand' => '',
                'description' => 'エレガントなパールイヤリング',
                'img_url' => 'public/img/earrings.jpg',
                'user_id' => 3,
                'condition_id' => 1,
            ],
        ];

        foreach ($params as $param) {
            Item::create($param);
        }

        $likes = [
            ['user_id' => 1, 'item_id' => 1],
            ['user_id' => 2, 'item_id' => 7],
            ['user_id' => 3, 'item_id' => 5],
            ['user_id' => 1, 'item_id' => 11],
            ['user_id' => 2, 'item_id' => 13],
        ];

        foreach ($likes as $like) {
            Like::create($like);
        }
    }
}
