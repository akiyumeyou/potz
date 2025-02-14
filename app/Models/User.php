<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\SupporterProfile;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'likes_count',
        'password',
        'real_name',
        'real_name_kana',
        'prefecture',
        'address1',
        'address2',
        'tel',
        'birthday',
        'membership_id',
        'gender',
        'last_login_at',
    ];
    protected $casts = [
        'last_login_at' => 'datetime', // ✅ 追加
    ];


// SupporterProfile リレーションを定義
public function supporterProfile()
{
    return $this->hasOne(SupporterProfile::class, 'user_id', 'id');
}

public function meetRooms()
{
    return $this->belongsToMany(MeetRoom::class, 'meetroom_members', 'user_id', 'meet_room_id')
        ->select('meet_rooms.*') // `meet_rooms` テーブルのカラムのみを取得
        ->withPivot('is_active', 'role', 'joined_at', 'left_at')
        ->withTimestamps();
}

public function membershipClass()
{
    return $this->belongsTo(MembershipClass::class, 'membership_id', 'id');
}

public function getAgeAttribute()
{
    if ($this->birthday) {
        return Carbon::parse($this->birthday)->age;
    }
    return '不明';
}

}
