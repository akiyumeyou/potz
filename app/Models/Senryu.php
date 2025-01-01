<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Senryu extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_name',
        'theme',
        's_text1',
        's_text2',
        's_text3',
        'img_path',
        'iine',
    ];
}
