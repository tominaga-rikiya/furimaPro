<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\SoldItem;
use App\Models\Like;
use App\Models\Comment;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'brand',
        'description',
        'img_url',
        'user_id',
        'condition_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function condition()
    {
        return $this->belongsTo(Condition::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function categoryItem()
    {
        return $this->hasMany(CategoryItem::class);
    }

    public function soldItem()
    {
        return $this->hasOne(SoldItem::class);
    }

    public function categories()
    {
        return $this->categoryItem->map(function ($item) {
            return $item->category;
        });
    }

    public function liked()
    {
        return Like::where(['item_id' => $this->id, 'user_id' => Auth::id()])->exists();
    }

    public function likeCount()
    {
        return Like::where('item_id', $this->id)->count();
    }

    public function getComments()
    {
        return Comment::where('item_id', $this->id)->get();
    }

    public function sold()
    {
        return SoldItem::where('item_id', $this->id)->exists();
    }

    public function mine()
    {
        return $this->user_id == Auth::id();
    }

    public static function scopeItem($query, $item_name)
    {
        return $query->where('name', 'like', '%' . $item_name . '%');
    }
}
