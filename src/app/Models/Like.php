<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Like extends Model
{
    use HasFactory;

    protected $primaryKey = ['user_id', 'item_id'];
    
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'item_id', 
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function liked($item_id)
    {
        $count = Like::where('item_id', $item_id)->where('user_id', Auth::id())->count();
        return $count > 0;
    }
}
