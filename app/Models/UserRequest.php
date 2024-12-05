<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRequest extends Model
{
    use HasFactory;

    protected $table = 'requests';

    protected $fillable = [
        'category3_id',
        'contents',
        'date',
        'time_start',
        'time', // 作業時間
        'spot',
        'address',
        'parking',
        'cost', // 時間単価
        'estimate', // 見積もり金額
        'requester_id',
        'status_id',
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
    public function category3()
    {
    return $this->belongsTo(Category3::class, 'category3_id');
    }

}
