<?php

namespace App\Http\Controllers;

use App\Models\MeetRoom;
use App\Models\UserRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class SupportController extends Controller
{
 // サポーター用依頼一覧の表示
public function index(Request $request)
{
    $user = Auth::user();

    if (!$user) {
        return redirect()->route('login')->with('error', 'ログインしてください。');
    }

    if ($user->membership_id !== 3) {
        return redirect()->route('requests.index')->with('error', 'サポーター区分ではありません。');
    }

    $filter = $request->input('filter', 'all'); // デフォルトは 'all'

    // フィルタに応じて案件を取得
    $requests = $this->getRequests($user, $filter);

    return view('supports.index', compact('requests', 'filter', 'user'));
}
// getRequests メソッド
private function getRequests($user, $filter)
{
    $query = UserRequest::with([
        'category3',
        'user' => function ($query) {
            $query->select('id', 'name');
        },
        'meetRoom' => function ($query) {
            $query->withCount(['members as supporter_count' => function ($subQuery) {
                $subQuery->where('role', 'supporter');
            }]);
        }
    ]);

    if ($filter === 'own') {
        $query->where('supporter_id', $user->id); // 自分の案件
    } elseif ($filter === 'new') {
        $query->where('status_id', 1); // 新規案件
    }

    return $query->get()
        ->map(function ($request) use ($user) {
            $request->status_name = match ($request->status_id) {
                1 => '新規依頼',
                2 => '調整中',
                3 => 'マッチング確定',
                4 => '終了',
                default => '不明',
            };

            // ボタン条件を設定
            $request->can_join = $request->status_id === 1 || $request->supporter_id === $user->id; // 打ち合わせ参加ボタン
            $request->can_recreate = $request->supporter_id === $user->id && $request->status_id !== 1; // 再依頼ボタン

            return $request;
        });
}


// ルームの状態によって色を決定するメソッド
private function determineColor($request, $userMeetRoomIds)
{
    if (!isset($request->meetRoom)) {
        return 'gray';
    }

    if (in_array($request->meetRoom->id, $userMeetRoomIds)) {
        return 'orange'; // 自分のルーム
    } elseif ($request->status_id === 4) {
        return 'gray'; // 終了案件
    } elseif ($request->meetRoom->supporter_count < $request->meetRoom->max_supporters) {
        return 'blue'; // 定員未満
    }

    return 'gray'; // 定員オーバー
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

