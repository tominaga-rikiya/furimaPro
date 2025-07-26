<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoldItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'item_id',
        'sending_postcode',
        'sending_address',
        'sending_building',
        'is_completed',
        'completed_at'
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    // 購入者のリレーション
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 商品のリレーション
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    // メッセージのリレーション
    public function messages()
    {
        return $this->hasMany(Message::class, 'sold_item_id');
    }

    // 最新メッセージのリレーション
    public function latestMessage()
    {
        return $this->hasOne(Message::class, 'sold_item_id')->latest();
    }

    // 評価のリレーション
    public function rating()
    {
        return $this->hasOne(Rating::class);
    }

    // 未読メッセージ数を取得（特定ユーザー向け）
    public function unreadMessagesCount($userId)
    {
        return $this->messages()
            ->where('user_id', '!=', $userId)
            ->where('is_read', false)
            ->count();
    }

    // 取引に関わっているかチェック
    public function isUserInTransaction($userId)
    {
        return $this->user_id === $userId || $this->item->user_id === $userId;
    }

    // 最新メッセージの日時を取得（アクセサー）
    public function getLastMessageAtAttribute()
    {
        $message = $this->messages()->latest()->first();
        return $message ? $message->created_at : null;
    }

    
}
