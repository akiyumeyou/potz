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
        return $this->belongsTo(Userrequest::class);
    }

    public function userrequest()
    {
    return $this->belongsTo(Userrequest::class, 'request_id');
    }
    public function meets()
    {
        return $this->hasMany(Meet::class, 'meet_room_id'); // meet_room_id に基づくリレーション
    }
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id'); // sender_id を User にリレーション
    }

}
