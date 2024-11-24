<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category3 extends Model
{
    use HasFactory;

    protected $table = 'category3'; // テーブル名を指定
    protected $fillable = ['category3', 'order_no', 'cost']; // 保存可能なカラム
}
