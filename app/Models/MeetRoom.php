<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
    ];

    public function request()
    {
        return $this->belongsTo(UserRequest::class);
    }

    public function userrequest()
    {
    return $this->belongsTo(UserRequest::class, 'request_id');
    }
    public function meets()
    {
        return $this->hasMany(Meet::class, 'meet_room_id'); // meet_room_id に基づくリレーション
    }
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id'); // sender_id を User にリレーション
    }
    public function members()
    {
        return $this->hasMany(MeetRoomMember::class, 'meet_room_id', 'id');
    }

    public function canJoin($userId)
{
    $activeMembers = $this->members()->where('is_active', 1)->count();
    $isUserInRoom = $this->members()->where('user_id', $userId)->exists();

    return $activeMembers < $this->max_members || $isUserInRoom;
}

}
