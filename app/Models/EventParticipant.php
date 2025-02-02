<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventParticipant extends Model
{
    protected $fillable = [
        'event_id',
        'user_id',
        'payment_method',
        'status',
        'payment_status',
        'amount_paid'
    ];

    protected $casts = [
        'status' => 'integer',
        'payment_status' => 'integer',
    ];

    // 🔹 ユーザーとのリレーションを定義
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 🔹 イベントとのリレーションを定義
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function getStatusTextAttribute()
    {
        return match ($this->status) {
            0 => '未承認',
            1 => '承認済み',
            default => '不明'
        };
    }

    public function getPaymentStatusTextAttribute()
    {
        return match ($this->payment_status) {
            0 => '未払い',
            1 => '支払い済み',
            default => '不明'
        };
    }
}

