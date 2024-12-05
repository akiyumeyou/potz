<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meet extends Model
{
    use HasFactory;

    protected $fillable = [
        'meet_room_id',
        'sender_id',
        'message',
        'image',
    ];

    public function room()
    {
        return $this->belongsTo(MeetRoom::class, 'meet_room_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
