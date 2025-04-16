<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class YouTube extends Model
{
    use HasFactory;

    protected $table = 'youtube';

    protected $fillable = [
        'youtube_link',
        'comment',
        'category',
        'like_count',
        'user_id'
    ];

    protected $with = ['user'];  // ユーザー情報を常にロード

    // app/Models/YouTube.php
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


}
