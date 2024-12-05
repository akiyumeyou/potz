<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; // HTTPリクエスト
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
            'request_id' => 'required|exists:user_requests,id', // `user_requests` テーブルに基づく
            'supporter_id' => 'required|exists:users,id', // サポーターIDが存在することを確認
        ]);

        // 該当するリクエストを取得
        $userRequest = UserRequest::findOrFail($validated['request_id']);

        // `user_requests` テーブルを更新
        $userRequest->supporter_id = $validated['supporter_id'];
        $userRequest->status_id = 3; // マッチング確定ステータス
        $userRequest->save();

        // `matchings` テーブルにレコードを作成
        $matching = Matching::create([
            'requester_id' => $userRequest->requester_id,
            'supporter_id' => $validated['supporter_id'],
            'meetroom_id' => $userRequest->meetroom_id,
            'status' => 3, // マッチング確定
            'matched_at' => now(),
        ]);

        // 成功メッセージを付けてリダイレクト
        return redirect()->route('requests.show', $userRequest->id)->with('success', 'マッチングが確定しました！');
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
