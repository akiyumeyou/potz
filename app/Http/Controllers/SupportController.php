<?php

namespace App\Http\Controllers;

use App\Models\MeetRoom;
use App\Models\UserRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;
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

    // 距離を計算してリクエストに追加
    $this->addDistanceToRequests($user, $requests);

    if (!$user->supporterProfile) {
        return back()->withErrors('サポーターのプロフィールが未設定です。');
    }


return view('supports.index', compact('requests', 'filter', 'user'));
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

    // MeetRoomMember にサポーターを追加
    $meetRoom->members()->firstOrCreate([
        'user_id' => $user->id,
    ], [
        'role' => 'supporter',
        'joined_at' => now(),
        'is_active' => true,
    ]);


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
