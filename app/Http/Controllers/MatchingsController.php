<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; // HTTPリクエスト
use Illuminate\Support\Facades\DB; // トランザクション用
use App\Models\UserRequest; // リクエストモデル
use App\Models\Matching; // マッチングモデル
use App\Models\MeetRoom; // ミートルームモデル

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
            'supporter_id' => 'required|exists:users,id', // サポーターIDが存在することを確認
        ]);

        DB::beginTransaction(); // トランザクション開始

        try {
            // 該当するリクエストを取得
            $userRequest = UserRequest::findOrFail($validated['request_id']);

            // `requests` テーブルを更新
            $userRequest->supporter_id = $validated['supporter_id'];
            $userRequest->status_id = 3; // マッチング確定ステータス
            $userRequest->save();

            // `matchings` テーブルにレコードを作成
            $matching = Matching::create([
                'request_id' => $userRequest->id, // リクエストID
                'requester_id' => $userRequest->requester_id, // リクエスト投稿者
                'supporter_id' => $validated['supporter_id'], // サポーター
                // 'meetroom_id' => $meetRoom->id, // ミートルームID
                'status' => 3, // マッチング確定ステータス
                'cost' => $userRequest->cost, // リクエストのコスト
                'time' => $userRequest->time, // リクエストの時間
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

