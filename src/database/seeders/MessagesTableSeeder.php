<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Message;
use App\Models\SoldItem;

class MessagesTableSeeder extends Seeder
{
    public function run()
    {
        $soldItems = SoldItem::with(['item.user', 'user'])->get();

        foreach ($soldItems as $soldItem) {
            $buyerId = $soldItem->user_id;        
            $sellerId = $soldItem->item->user_id; 

            Message::create([
                'sold_item_id' => $soldItem->id,
                'user_id' => $buyerId,
                'content' => 'はじめまして。こちらの商品を購入させていただきました。',
                'is_read' => false,
                'created_at' => now()->subHours(rand(1, 48)),
                'updated_at' => now()->subHours(rand(1, 48)),
            ]);

            Message::create([
                'sold_item_id' => $soldItem->id,
                'user_id' => $sellerId,
                'content' => 'ご購入ありがとうございます。発送準備をいたします。',
                'is_read' => true,
                'created_at' => now()->subHours(rand(1, 24)),
                'updated_at' => now()->subHours(rand(1, 24)),
            ]);
        }
    }
}
