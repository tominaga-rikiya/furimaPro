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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'sold_item_id');
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class, 'sold_item_id')->latest();
    }

    public function rating()
    {
        return $this->hasOne(Rating::class);
    }

    public function unreadMessagesCount($userId)
    {
        return $this->messages()
            ->where('user_id', '!=', $userId)
            ->where('is_read', false)
            ->count();
    }

    public function isUserInTransaction($userId)
    {
        return $this->user_id === $userId || $this->item->user_id === $userId;
    }

    public function getLastMessageAtAttribute()
    {
        $message = $this->messages()->latest()->first();
        return $message ? $message->created_at : null;
    }
}
