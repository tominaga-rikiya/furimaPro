<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Comment;

class CommentsTableSeeder extends Seeder
{
    public function run()
    {
        $params = [
            [
                'user_id' => 1,
                'item_id' => 2,
                'comment' => 'この商品の保証期間はどのくらいですか？',
            ],
            [
                'user_id' => 2,
                'item_id' => 2,
                'comment' => '購入から1年間の保証が残っています。',
            ],
            [
                'user_id' => 1,
                'item_id' => 5,
                'comment' => 'スペック詳細を教えていただけますか？',
            ],
            [
                'user_id' => 2,
                'item_id' => 7,
                'comment' => 'とても素敵なバッグですね！',
            ],
            [
                'user_id' => 1,
                'item_id' => 7,
                'comment' => 'ありがとうございます！大切に使っていました。',
            ],
            [
                'user_id' => 2,
                'item_id' => 9,
                'comment' => 'コーヒーの味が変わりそうで興味があります。',
            ],
        ];

        foreach ($params as $param) {
            Comment::create($param);
        }
    }
}
