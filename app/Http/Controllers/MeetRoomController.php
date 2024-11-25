<?php

namespace App\Http\Controllers;

use App\Models\MeetRoom;
use Illuminate\Http\Request;

class MeetRoomController extends Controller
{
    // チャットルームを表示
    public function show($request_id)
    {
        // request_id で MeetRoom を検索
        $meetRoom = MeetRoom::where('request_id', $request_id)->first();

        if (!$meetRoom) {
            abort(404, 'MeetRoom not found.');
        }

        // 必要なデータをビューに渡す
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

        return redirect()->route('meet_rooms.show', $meetRoom->request_id)->with('success', 'メッセージを送信しました。');
    }
}
