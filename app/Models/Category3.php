<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category3 extends Model
{
    use HasFactory;

    // テーブル名を明示的に指定
    protected $table = 'category3';

    // 保存可能なカラムを定義
    protected $fillable = [
        'category3', // カテゴリ名
        'order_no',  // 表示順
        'cost',      // コスト
    ];

    /**
     * UserRequest とのリレーション
     * カテゴリに関連付けられたリクエストを取得
     */
    public function requests()
    {
        return $this->hasMany(UserRequest::class, 'category3_id');
    }
}
