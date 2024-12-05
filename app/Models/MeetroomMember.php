<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetRoomMember extends Model
{
    use HasFactory;

    // テーブル名
    protected $table = 'meetroom_members';

    // 一括代入可能なカラム
    protected $fillable = [
        'meet_room_id',
        'user_id',
        'role',
        'is_active',
        'joined_at',
        'left_at',
    ];

    /**
     * MeetRoom とのリレーション
     * このメンバーが所属するミーティングルームを取得
     */
    public function meetRoom()
    {
        return $this->belongsTo(MeetRoom::class, 'meet_room_id', 'id');
    }

    /**
     * User とのリレーション
     * このメンバーに関連するユーザー情報を取得
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
