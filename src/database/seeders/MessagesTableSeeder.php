<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Message;
use Carbon\Carbon;

class MessagesTableSeeder extends Seeder
{
    public function run()
    {
        $params = [
            [
                'sold_item_id' => 1,
                'user_id' => 1,
                'content' => 'こんにちは。HDDを購入させていただきました。よろしくお願いします。',
                'is_read' => true,
                'created_at' => Carbon::now()->subDays(9),
            ],
            [
                'sold_item_id' => 1,
                'user_id' => 2,
                'content' => 'ご購入ありがとうございます。明日発送予定です。',
                'is_read' => true,
                'created_at' => Carbon::now()->subDays(9),
            ],
            [
                'sold_item_id' => 1,
                'user_id' => 2,
                'content' => '発送が完了しました。追跡番号は123456789です。',
                'is_read' => true,
                'created_at' => Carbon::now()->subDays(8),
            ],
            [
                'sold_item_id' => 1,
                'user_id' => 1,
                'content' => '商品を受け取りました。ありがとうございました！',
                'is_read' => true,
                'created_at' => Carbon::now()->subDays(7),
            ],
            [
                'sold_item_id' => 2,
                'user_id' => 2,
                'content' => 'ショルダーバッグを購入しました。発送をお願いします。',
                'is_read' => true,
                'created_at' => Carbon::now()->subDays(3),
            ],
            [
                'sold_item_id' => 2,
                'user_id' => 1,
                'content' => 'ご購入ありがとうございます。本日発送いたします。',
                'is_read' => false,
                'created_at' => Carbon::now()->subDays(2),
            ],
        ];

        foreach ($params as $param) {
            Message::create($param);
        }
    }
}
