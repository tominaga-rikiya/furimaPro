<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'sold_item_id',
        'user_id',
        'content',
        'image',
        'is_read'
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function soldItem()
    {
        return $this->belongsTo(SoldItem::class, 'sold_item_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
