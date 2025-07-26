<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email_complete',
        'email_message'
    ];

    protected $casts = [
        'email_complete' => 'boolean',
        'email_message' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
