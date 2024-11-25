<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category3 extends Model
{
    use HasFactory;

    protected $table = 'category3'; // テーブル名を指定
    protected $fillable = [
        'category3_id', // カテゴリIDをfillableに追加
        'contents',
        'date',
        'time',
        'spot',
        'address',
        'requester_id',
        'status_id',
    ];
    public function requests()
    {
        return $this->hasMany(Userrequest::class, 'category3_id');
    }

}
