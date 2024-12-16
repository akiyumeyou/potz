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

        // サポーターが所属するルームのIDを取得
        $userMeetRoomIds = $user->meetRooms()
            ->wherePivot('is_active', 1)
            ->pluck('id')
            ->toArray();

        // 依頼一覧を取得
        $requests = UserRequest::with([
            'category3',
            'user' => function ($query) {
                $query->select('id', 'address1', 'name');
            },
            'meetRoom' => function ($query) {
                $query->withCount(['members as supporter_count' => function ($subQuery) {
                    $subQuery->where('role', 'supporter'); // サポーターのみカウント
                }]);
            }
        ])
        ->select('id', 'category3_id', 'requester_id', 'spot', 'date', 'time_start', 'time', 'status_id')
        ->get()
        ->map(function ($request) use ($userMeetRoomIds, $user) {
            $request->status_name = match ($request->status_id) {
                1 => '新規依頼',
                2 => '打ち合わせ中',
                3 => 'マッチング確定',
                4 => '終了',
                default => '不明',
            };

            // 状態による色分けとアクション
            if (in_array($request->meetRoom->id, $userMeetRoomIds)) {
                // 自分が所属するルーム
                $request->color = 'orange';
                $request->can_join = true;
            } elseif ($request->status_id === 4) {
                // 終了案件は常にグレー
                $request->color = 'gray';
                $request->can_join = false;
            } elseif ($request->meetRoom->supporter_count < $request->meetRoom->max_supporters) {
                // 定員未満のルーム
                $request->color = 'blue';
                $request->can_join = true;
            } else {
                // 定員オーバーのルーム
                $request->color = 'gray';
                $request->can_join = false;
            }

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
    $userRequest = UserRequest::with('meetRoom')->findOrFail($requestId);

    $meetRoom = $userRequest->meetRoom;

    // ルームが存在しない場合はエラー
    if (!$meetRoom) {
        return redirect()->route('supports.index')->with('error', 'この依頼にはルームが作成されていません。');
    }

   // サポーター人数のカウント
   $supporterCount = $meetRoom->members()
   ->where('role', 'supporter')
   ->count();

if ($supporterCount >= $meetRoom->max_supporters) {
   return redirect()->route('supports.index')->withErrors('ルームは定員に達しています。');
}

// MeetRoomMember にサポーターを追加
$meetRoom->members()->firstOrCreate([
   'user_id' => $user->id,
], [
   'role' => 'supporter',
   'joined_at' => now(),
   'is_active' => true,
]);

    // ステータスとサポーターIDを更新
    $userRequest->update([
        'status_id' => 2,
        'supporter_id' => $user->id,
    ]);

    return redirect()->route('meet_rooms.show', ['request_id' => $meetRoom->request_id])
        ->with('success', 'ルームに参加しました。');
}

}

