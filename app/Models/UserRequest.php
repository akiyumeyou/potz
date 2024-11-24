<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Userrequest extends Model
{
    use HasFactory;

    protected $table = 'requests';

    protected $fillable = [
        'contents',
        'date',
        'time',
        'spot',
        'address',
        'requester_id',
    ];

    // ユーザーとのリレーション
    public function user()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }
    public function getStatusNameAttribute()
    {
        return match ($this->status_id) {
            1 => '準備中',
            2 => '調整中',
            3 => '確定',
            4 => '完了',
            6 => 'キャンセル',
            9 => '削除',
            default => '不明',
        };
    }
}
