<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;


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
        'last_read_meet_id',
    ];
    /**
     * メンバーごとに既読メッセージを管理
     */
    //
    public function getUnreadCount()
{
    // ログイン情報を記録
    Log::info('Calculating unread count for member:', [
        'meet_room_id' => $this->meet_room_id,
        'user_id' => $this->user_id,
        'last_read_meet_id' => $this->last_read_meet_id,
    ]);

    // 未読メッセージのカウントを計算
    $unreadCount = Meet::where('meet_room_id', $this->meet_room_id)
        ->where('id', '>', $this->last_read_meet_id ?? 0)
        ->count();

    // 計算結果をログ出力
    Log::info('Unread message count calculated:', [
        'unread_count' => $unreadCount,
    ]);

    return $unreadCount;
}


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
