<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserRequest;
use App\Models\MeetRoom;
use App\Http\Controllers\Matching;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class RequestController extends Controller
{
    /**
     * 依頼一覧を表示
    */
    public function index()
    {
        $user = Auth::user();

        // ログインしていない場合、ログインページにリダイレクト
        if (!$user) {
            return redirect()->route('login')->with('error', 'ログインしてください。');
        }

        // 一般会員でプロフィール登録が未完了の場合
        $requiresProfileCompletion = $user->membership_id == 1 && !$user->profile_completed;

        // ログイン中のユーザーの依頼を取得（降順でソート）
        $requests = UserRequest::where('requester_id', $user->id)
            ->orderBy('date', 'desc') // 日付の降順でソート
            ->get();

        // membership_id を取得
        $membershipId = $user->membership_id;

        // ac_id を取得
        $acId = optional($user->supporterProfile)->ac_id;

        // デバッグログ
        logger()->info('ログイン中のユーザー:', [
            'id' => $user->id,
            'membership_id' => $membershipId,
            'ac_id' => $acId,
            'requiresProfileCompletion' => $requiresProfileCompletion,
        ]);

        // ビューにデータを渡す
        return view('requests.index', compact('requests', 'user', 'membershipId', 'acId', 'requiresProfileCompletion'));
    }


    /**
     * 依頼作成フォームを表示
    */
    public function create()
    {
        // カテゴリデータを取得
        $categories = DB::table('category3')->select('id', 'category3', 'cost')->get();

        return view('requests.create', compact('categories'));
    }

/**
     * 再依頼フォームを表示
     */
    public function createFromRequest($from_request)
    {
        $user = Auth::user();

        // ログインチェック
        if (!$user) {
            return redirect()->route('login')->with('error', 'ログインしてください。');
        }

        // 再依頼元のリクエストを取得
        $originalRequest = UserRequest::where('id', $from_request)
            ->where(function ($query) use ($user) {
                $query->where('requester_id', $user->id) // 依頼者の場合
                      ->orWhereHas('supporter', function ($q) use ($user) {
                          $q->where('id', $user->id); // サポーターの場合
                      });
            })
            ->firstOrFail();
                // 現在ログインしているサポーターが対応する案件のみ処理を許可
        if ($originalRequest->supporter_id !== null && $originalRequest->supporter_id !== $user->id) {
        return redirect()->route('requests.index')->with('error', 'この案件を再依頼する権限がありません。');
    }

        // 日時は再設定（依頼者には空欄、サポーターにはデフォルト値）
        if ($originalRequest->requester_id === $user->id) {
            $originalRequest->date = null; // 依頼者の場合は空欄
        } else {
            $originalRequest->date = now()->addDays(1)->format('Y-m-d'); // サポーターの場合は翌日
            $originalRequest->time_start = '10:00'; // デフォルトの開始時刻
        }

        // カテゴリデータを取得
        $categories = DB::table('category3')->select('id', 'category3', 'cost')->get();

        // ビューにデータを渡す
        return view('requests.create', compact('categories', 'originalRequest'));
    }

    /**
     * 依頼を保存
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            // バリデーション
            $validated = $request->validate([
                'category3_id' => 'required|exists:category3,id',
                'contents' => 'required|string|max:1000',
                'date' => 'required|date',
                'time_start' => 'required|date_format:H:i',
                'time' => 'required|numeric|min:0.5|max:8.0',
                'spot' => 'nullable|string|max:255',
                'address' => 'nullable|string|max:255',
                'parking' => 'required|integer|in:1,2',
            ]);

            // 緯度経度、住所の初期値
            $latitude = null;
            $longitude = null;
            $address = null;

            // 自宅の場合、ユーザーの住所を結合
            if ($request->input('spot') === '自宅') {
                $user = Auth::user();
                $address = implode(' ', array_filter([$user->prefecture, $user->address1, $user->address2]));

                if (!empty($address)) {
                    [$latitude, $longitude] = $this->getCoordinates($address);
                }
            }
            // その他の場合、入力された住所を使用
            elseif ($request->input('spot') === 'その他' && !empty($request->input('address'))) {
                $address = $request->input('address');
                [$latitude, $longitude] = $this->getCoordinates($address);
            }

            // ログで確認
            Log::info('Address to save:', ['address' => $address]);

            // 元の依頼情報を取得（再依頼の場合のみ存在）
            $originalRequestId = $request->input('original_request_id');
            $originalRequest = $originalRequestId ? UserRequest::find($originalRequestId) : null;

            // 依頼者とサポーターの設定
            $isSupporterAction = Auth::user()->membership_id === 3; // サポーターかどうか
            $requesterId = $originalRequest ? $originalRequest->requester_id : Auth::id(); // 元の依頼者を優先
            $supporterId = $isSupporterAction ? Auth::id() : ($originalRequest->supporter_id ?? null); // サポーターの場合設定

            // ステータスの設定
            $statusId = $isSupporterAction ? 2 : 1; // サポーターの場合はステータスを２に設定

            // カテゴリコスト取得
            $cost = DB::table('category3')->where('id', $validated['category3_id'])->value('cost');
            if (!$cost) {
                return back()->withErrors(['category3_id' => 'カテゴリに単価が設定されていません。']);
            }

 // 距離と交通費計算
 $distance = $originalRequest ? $originalRequest->distance : null; // 再依頼の場合は元のリクエストの距離を使用
 $transportCost = $distance ? $distance * 15 * 2 : 400; // 距離がある場合は計算、ない場合は仮値400円

 // カテゴリコスト取得
 $cost = DB::table('category3')->where('id', $validated['category3_id'])->value('cost');
 if (!$cost) {
     return back()->withErrors(['category3_id' => 'カテゴリに単価が設定されていません。']);
 }

 // 見積もり金額を計算
 $estimate = ($cost * $validated['time']) + $transportCost;

 // ログで確認
 Log::info('Coordinates and distance:', [
     'latitude' => $latitude,
     'longitude' => $longitude,
     'distance' => $distance,
 ]);

 // 新しい依頼の作成
 $newRequest = UserRequest::create([
     'category3_id' => $validated['category3_id'],
     'contents' => $validated['contents'],
     'date' => $validated['date'],
     'time_start' => $validated['time_start'],
     'time' => $validated['time'],
     'spot' => $validated['spot'] ?? null,
     'address' => $address, // 修正
     'latitude' => $latitude,
     'longitude' => $longitude,
     'parking' => $validated['parking'],
     'cost' => $cost,
     'estimate' => $estimate, // 再計算された見積もり金額
     'distance' => $distance, // 再依頼元の距離を保存
     'requester_id' => $originalRequest ? $originalRequest->requester_id : Auth::id(), // 再依頼元の依頼者ID
     'supporter_id' => $originalRequest ? $originalRequest->supporter_id : null, // 再依頼元のサポーターID
     'status_id' => $originalRequest ? 2 : 1, // 再依頼の場合はステータスを2
 ]);

            // MeetRoom 作成
            $meetRoom = MeetRoom::create([
                'request_id' => $newRequest->id,
                'max_supporters' => 1, // サポーター1人に設定
            ]);

            // MeetRoom メンバー登録
            DB::table('meetroom_members')->insert([
                [
                    'meet_room_id' => $meetRoom->id,
                    'user_id' => $requesterId, // 依頼者
                    'role' => 'requester',
                    'is_active' => 1,
                    'joined_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'meet_room_id' => $meetRoom->id,
                    'user_id' => 3, // 管理者（仮ID）
                    'role' => 'admin',
                    'is_active' => 1,
                    'joined_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);

            // サポーターも追加
            if ($supporterId) {
                DB::table('meetroom_members')->insert([
                    'meet_room_id' => $meetRoom->id,
                    'user_id' => $supporterId,
                    'role' => 'supporter',
                    'is_active' => 1,
                    'joined_at' => now(),
                ]);
            }

            DB::commit();

            return redirect()->route('requests.index')->with('success', '依頼が登録されました。');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('依頼の保存中にエラーが発生しました: ' . $e->getMessage());
            return back()->with('error', '依頼の保存に失敗しました。');
        }
    }


    public function edit($id)
    {
        $userRequest = UserRequest::findOrFail($id);

        // 必要に応じて打ち合わせ中の案件か確認する
        if ($userRequest->status_id !== 3) {
            return redirect()->route('requests.index')->withErrors('編集可能な依頼ではありません。');
        }

        return view('requests.edit', compact('userRequest'));
    }

    public function update(Request $request, $id)
    {
        try {
            // バリデーション
            $validated = $request->validate([
                'contents' => 'required|string|max:1000',
                'date' => 'required|date_format:Y-m-d',
                'time_start' => 'nullable|date_format:H:i',
                'time' => 'nullable|numeric|min:0.5|max:8.0',
            ]);

            // データベースからリクエストを取得
            $userRequest = UserRequest::findOrFail($id);

            // 更新前の time を保持
            $originalTime = $userRequest->time;

            // 更新処理
            $userRequest->contents = $validated['contents'] ?? $userRequest->contents;
            $userRequest->date = $validated['date'] ?? $userRequest->date;
            $userRequest->time_start = $validated['time_start'] ?? $userRequest->time_start;
            $userRequest->time = $validated['time'] ?? $userRequest->time;

        // time が変更された場合にのみ見積もりを再計算
        if ($originalTime !== $validated['time']) {
            $cost = $userRequest->cost;

            if (!$cost) {
                throw new Exception('コストが設定されていません。');
            }

            // 距離が設定されていない場合、交通費計算はスキップ
            $distance = $userRequest->distance;
            if (!$distance) {
                throw new Exception('距離が設定されていません。');
            }

            // 交通費計算 (往復を考慮)
            $transportCost = $distance * 15 * 2; // 距離 × 単価（15円） × 往復

            // 見積もり金額を再計算
            $userRequest->estimate = ($cost * $validated['time']) + $transportCost;
        }

            $userRequest->save();

            // MeetRoom の ID を取得
            $meetRoom = MeetRoom::where('request_id', $userRequest->id)->first();
            if (!$meetRoom) {
                throw new Exception('関連するMeetRoomが見つかりません');
            }

            // ログ: 更新成功
            // logger()->info('Request updated successfully:', ['id' => $userRequest->id]);

            // 更新後、meet_rooms.show にリダイレクト
            return redirect()->route('meet_rooms.show', ['request_id' => $userRequest->id])
            ->with('success', '依頼が更新されました！');
        } catch (\Exception $e) {
            // エラーログ
            logger()->error('Error during request update:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return back()->withErrors('更新中にエラーが発生しました。');
        }
    }


    /**
     * リクエスト詳細表示
     */
    public function show($id)
    {
        $meetRoom = MeetRoom::findOrFail($id);
        $userRequest = UserRequest::findOrFail($meetRoom->request_id);
        $userRequest = UserRequest::with('category3')->findOrFail($id);

        // $matching をビューに渡す
        return view('requests.show', compact('meetRoom', 'userRequest'));
    }

    private function getCoordinates($address)
    {
        try {
            $apiKey = env('GOOGLE_MAPS_API_KEY');
            $url = "https://maps.googleapis.com/maps/api/geocode/json";

            // APIリクエスト
            $response = Http::get($url, [
                'address' => $address, // 住所
                'key' => $apiKey,      // Google APIキー
            ]);

            $data = $response->json();

            // レスポンスをログに記録
            Log::info('Google Maps Geocoding API Response:', $data);

            // レスポンスから緯度経度を取得
            if (!empty($data['results'])) {
                $geometry = $data['results'][0]['geometry']['location'];
                return [$geometry['lat'], $geometry['lng']];
            }

            // 結果が空の場合を記録
            Log::warning('Google Maps API returned no results for address:', ['address' => $address]);
        } catch (\Exception $e) {
            Log::error('Google Maps Geocoding API error: ' . $e->getMessage());
        }

        return [null, null]; // デフォルト値
    }
}
