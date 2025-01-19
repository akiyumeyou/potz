<?php

namespace App\Http\Controllers;

use App\Models\MeetRoom;
use App\Models\Meet;
use App\Models\UserRequest;
use App\Models\User; // User モデルのインポート
use App\Models\MeetRoomMember;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Notifications\SupportNotification;
use App\Services\MeetRoomService;


class SupportController extends Controller
{
    private $meetRoomService; // サービスを保持するプロパティ

    // コンストラクタで依存性注入
    public function __construct(MeetRoomService $meetRoomService)
    {
        $this->meetRoomService = $meetRoomService;
    }

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

    // 距離を計算してリクエストに追加
    $this->addDistanceToRequests($user, $requests);

    if (!$user->supporterProfile) {
        return back()->withErrors('サポーターのプロフィールが未設定です。');
    }

    foreach ($requests as $request) {
        $meetRoom = $request->meetRoom; // 関連する MeetRoom を取得

        // リクエストと関連する MeetRoom の情報をログ出力
        Log::info('Processing Request and MeetRoom', [
            'request_id' => $request->id,
            'meet_room_id' => $meetRoom ? $meetRoom->id : 'null',
        ]);

        if ($meetRoom) {
            // 現在のユーザーに関連するメンバー情報を取得
            $member = $meetRoom->members->where('user_id', $user->id)->first();

            // ユーザー情報をログ出力
            Log::info('MeetRoom Member Information', [
                'user_id' => $user->id,
                'member_id' => $member ? $member->id : 'null',
            ]);

            if ($member) {
                // 未読件数を計算
                $unreadCount = $this->meetRoomService->getUnreadCount(
                    $meetRoom->id,
                    $member->last_read_meet_id
                );

                // 未読件数をログ出力
                Log::info('Unread Count for Request', [
                    'request_id' => $request->id,
                    'unread_count' => $unreadCount,
                ]);

                $request->unread_count = $unreadCount;
            } else {
                $request->unread_count = 0; // メンバーが存在しない場合
                Log::info('No Member Found for MeetRoom', ['meet_room_id' => $meetRoom->id]);
            }
        } else {
            $request->unread_count = 0; // MeetRoom が存在しない場合
            Log::info('No MeetRoom Found for Request', ['request_id' => $request->id]);
        }
    }

// サポーターの「ありがとう」の合計を取得
$totalLikes = User::sum('likes_count');

return view('supports.index', compact('requests', 'filter', 'user', 'totalLikes'));
}


// getRequests メソッド
private function getRequests($user, $filter)
{
    $query = UserRequest::with([
        'category3',
        'user' => function ($query) {
            $query->select('id', 'name', 'gender', 'address1', 'birthday'); // 必要なユーザー情報を取得
        },
        'meetRoom' => function ($query) {
            $query->withCount(['members as supporter_count' => function ($subQuery) {
                $subQuery->where('role', 'supporter'); // サポーター人数をカウント
            }]);
        }
    ]);

    // フィルター条件に応じてクエリを修正
    if ($filter === 'own') {
        $query->where('supporter_id', $user->id); // 自分の案件
    } elseif ($filter === 'new') {
        $query->where('status_id', 1); // 新規案件
    }

    $requests = $query->get();


    return $query->get()->map(function ($request) use ($user) {
        $request->status_name = match ($request->status_id) {
            1 => '新規依頼',
            2 => '打ち合わせ中',
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


/**
     * 年齢を動的に算出するアクセサ
     *
     */
    public function getAgeAttribute()
    {
        if ($this->birthday) {
            return Carbon::parse($this->birthday)->age; // Carbonで年齢を計算
        }
        return null; // 誕生日がない場合は null
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
public function joinRoom($id)
{
    $user = Auth::user();

    if (!$user) {
        return redirect()->route('login')->with('error', 'ログインしてください。');
    }

    if ($user->membership_id !== 3) {
        return redirect()->route('supports.index')->with('error', 'サポーター区分ではありません。');
    }

    // 対応する依頼を取得
    $userRequest = UserRequest::with('meetRoom')->findOrFail($id);

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


// 距離と見積もりを計算して保存
$distance = $this->calculateDistance(
    $user->supporterProfile->latitude ?? 0,
    $user->supporterProfile->longitude ?? 0,
    $userRequest->latitude ?? 0,
    $userRequest->longitude ?? 0
);
$estimate = ($userRequest->cost * $userRequest->time) + (15 * $distance * 2);

$userRequest->update([
    'status_id' => 2,
    'supporter_id' => $user->id,
    'distance' => $distance,
    'estimate' => $estimate,
]);

$requester = $userRequest->user; // 依頼者（ユーザーオブジェクト）
if ($requester) {
    $requester->notify(new SupportNotification(
        $user->name, // サポーター名
        route('requests.show', ['id' => $userRequest->id]) // 通知リンク先
    ));
}

// MeetRoomMember の作成または取得
$member = MeetRoomMember::firstOrCreate(
    [
        'meet_room_id' => $meetRoom->id,
        'user_id' => $user->id, // サポーターの ID を指定
    ],
    [
        'role' => 'supporter',
        'is_active' => true,
        'joined_at' => now(),
    ]
);

// ルーム内の最新メッセージ ID を取得
$latestMessageId = Meet::where('meet_room_id', $meetRoom->id)->max('id');

// last_read_meet_id を更新
$member->last_read_meet_id = $latestMessageId;
$member->save();

// 打ち合わせルームのビューを表示
return redirect()->route('meet_rooms.show', $id)->with('success', 'ルームに入りました。');
}


// リクエストに距離を追加
private function addDistanceToRequests($user, $requests)
{
    $supporterLat = $user->supporterProfile->latitude ?? null;
    $supporterLng = $user->supporterProfile->longitude ?? null;

    if ($supporterLat && $supporterLng) {
        foreach ($requests as $request) {
            if ($request->latitude && $request->longitude) {
                $request->distance = $this->calculateDistance(
                    $supporterLat,
                    $supporterLng,
                    $request->latitude,
                    $request->longitude
                );
            } else {
                $request->distance = null; // 緯度経度が設定されていない場合
            }
        }
    }
}

// 距離計算
private function calculateDistance($lat1, $lon1, $lat2, $lon2)
{
    $earthRadius = 6371; // 地球の半径 (km)

    // 度をラジアンに変換
    $lat1 = deg2rad($lat1);
    $lon1 = deg2rad($lon1);
    $lat2 = deg2rad($lat2);
    $lon2 = deg2rad($lon2);

    // 緯度と経度の差
    $latDiff = $lat2 - $lat1;
    $lonDiff = $lon2 - $lon1;

    // Haversine Formula
    $a = sin($latDiff / 2) * sin($latDiff / 2) +
        cos($lat1) * cos($lat2) *
        sin($lonDiff / 2) * sin($lonDiff / 2);

    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    $distance = $earthRadius * $c; // 距離 (km)

    return ceil($distance); // 繰上げ整数で返す
}

}
