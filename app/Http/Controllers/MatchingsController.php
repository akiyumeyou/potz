<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; // HTTPリクエスト
use Illuminate\Support\Facades\DB; // トランザクション用
use App\Models\UserRequest; // リクエストモデル
use App\Models\Matching; // マッチングモデル
use App\Models\MeetRoom; // ミートルームモデル
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class MatchingsController extends Controller
{
    /**
     * マッチング確定処理
     */
    public function confirm(Request $request)
    {
        // 入力データの検証
        $validated = $request->validate([
            'request_id' => 'required|exists:requests,id', // `requests` テーブルに基づく
            'supporter_id' => 'required|exists:users,id',
        ]);

        DB::beginTransaction(); // トランザクション開始


        try {
            // 該当するリクエストを取得（関連データを事前にロード）
            $userRequest = UserRequest::with('meetRoom')->findOrFail($validated['request_id']);

            // 距離が設定されていない場合はログ出力
            if (is_null($userRequest->distance)) {
                Log::warning("Distance is null for request ID: {$userRequest->id}");
            }

// supporter_id が正しく設定されているか確認
// if ($userRequest->supporter_id !== $request->input('supporter_id')) {
//     throw new \Exception('不正なサポーターIDです。');
// }

            // `requests` テーブルを更新
            $userRequest = UserRequest::find($request->input('request_id'));
            $userRequest->supporter_id = $request->input('supporter_id'); // サポーターIDを保存
            $userRequest->confirmed_by = Auth::id(); // 誰が確定を押したか保存
            $userRequest->status_id = 3; // 成立中に更新
            $userRequest->save();

            // `matchings` テーブルにレコードを作成
            $matching = Matching::create([
                'request_id' => $userRequest->id, // リクエストID
                'requester_id' => $userRequest->requester_id, // リクエスト投稿者
                'supporter_id' => $validated['supporter_id'], // サポーター
                'status' => 3, // マッチング確定ステータス
                'cost' => $userRequest->cost, // リクエストのコスト
                'time' => $userRequest->time, // リクエストの時間
                'distance' => $userRequest->distance ?? 4, // null の場合はデフォルト値 0 を設定
                'meetroom_id' => $userRequest->meetRoom->id ?? null, // ミートルームID
                'matched_by_user_id' => $request->user()->id, // マッチング確定を押したユーザー
                'matched_at' => now(), // マッチング確定日時
            ]);

            DB::commit(); // コミット

            // 成功メッセージを付けてリダイレクト
            return redirect()->route('meet_rooms.show', $userRequest->id)->with('success', 'マッチングが確定しました！');
        } catch (\Exception $e) {
            DB::rollBack(); // エラー時にロールバック
            return back()->withErrors('マッチング確定中にエラーが発生しました。もう一度お試しください。');
        }
    }

    /**
     * マッチング詳細の表示
     */
    public function show($id)
    {
        // マッチング情報を取得
        $matching = Matching::with(['requester', 'supporter', 'meetRoom'])->findOrFail($id);

        return view('matchings.show', compact('matching'));
    }
}

