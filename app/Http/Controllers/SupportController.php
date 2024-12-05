<?php

namespace App\Http\Controllers;

use App\Models\MeetRoom;
use App\Models\UserRequest;
use Illuminate\Support\Facades\Auth;

class SupportController extends Controller
{
    // サポーター用依頼一覧の表示
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'ログインしてください。');
        }

        if ($user->membership_id !== 3) {
            return redirect()->route('requests.index')->with('error', 'サポーター区分ではありません。');
        }

        // 依頼一覧を取得
        $requests = UserRequest::with(['category3', 'user' => function ($query) {
            $query->select('id', 'address1', 'name');
        }])
        ->select('id', 'category3_id', 'requester_id', 'spot', 'date', 'time_start', 'time', 'status_id')
        ->get()
        ->map(function ($request) {
            $request->status_name = match ($request->status_id) {
                1 => '準備中',
                2 => '調整中',
                3 => 'マッチング確定',
                
                default => '不明',
            };
            return $request;
        });

        return view('supports.index', compact('requests', 'user'));
    }

    // 打ち合わせルームに参加
    public function joinRoom($requestId)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'ログインしてください。');
        }

        if ($user->membership_id !== 3) {
            return redirect()->route('supports.index')->with('error', 'サポーター区分ではありません。');
        }

        // 対応する依頼を取得
        $userRequest = UserRequest::findOrFail($requestId);


        // ステータスとサポーターIDを更新
        $userRequest->update([
            'status_id' => 2,
            'supporter_id' => $user->id,
        ]);

        // MeetRoom を作成または取得
        $meetRoom = MeetRoom::firstOrCreate(['request_id' => $userRequest->id]);

        // MeetRoomMember に記録
        $meetRoom->members()->firstOrCreate([
            'user_id' => $user->id,
        ], [
            'role' => 'supporter',
            'joined_at' => now(),
            'is_active' => true,
        ]);

        return redirect()->route('meet_rooms.show', ['request_id' => $meetRoom->request_id])
            ->with('success', 'ルームに参加しました。');
    }
}

