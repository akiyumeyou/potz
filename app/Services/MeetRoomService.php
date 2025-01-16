<?php

namespace App\Services;

use App\Models\Meet;

class MeetRoomService
{
    public function getUnreadCount($meetRoomId, $lastReadMeetId)
    {
        return Meet::where('meet_room_id', $meetRoomId)
            ->where('id', '>', $lastReadMeetId ?? 0)
            ->count();
    }
}
