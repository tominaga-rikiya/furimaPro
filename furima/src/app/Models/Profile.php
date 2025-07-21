<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'building_name',
        'postal_code',
        'profile_imge',
        'address',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
