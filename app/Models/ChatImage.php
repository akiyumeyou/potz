<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatImage extends Model
{
    use HasFactory;

    protected $fillable = ['chat_id', 'image_path'];

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }
}
