<?php

namespace App\Http\Controllers;

use App\Models\MeetRoom;
use App\Models\Meet;
use Illuminate\Http\Request;

class MeetRoomController extends Controller
{
    // チャットルームを表示
    public function show($id)
    {
        $meetRoom = MeetRoom::with('meets.sender')->findOrFail($id);

        return view('requests.show', compact('meetRoom'));
    }

    // メッセージを投稿
    public function store(Request $request, $id)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $meetRoom = MeetRoom::findOrFail($id);

        $meetRoom->meets()->create([
            'sender_id' => auth()->id(),
            'message' => $validated['message'],
        ]);

        return redirect()->route('meet_rooms.show', $id)->with('success', 'メッセージを送信しました。');
    }
}
