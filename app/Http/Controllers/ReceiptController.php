<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Matching;
use App\Models\UserRequest; // 依頼テーブルモデルをインポート
use Barryvdh\DomPDF\Facade\Pdf;
use App\Notifications\ReceiptNotification;
use Carbon\Carbon;


class ReceiptController extends Controller
{
    public function show($request_id)
    {
        $matching = Matching::where('request_id', $request_id)->firstOrFail();
        $userRequest = UserRequest::findOrFail($request_id);

        if ($matching->syousyu_flg == 1 && !empty($matching->closed_at)) {
            try {
                // `closed_at` が正しい日付ならパースする
                $receiptDate = Carbon::parse($matching->closed_at)->format('Y年m月d日');
            } catch (\Exception $e) {
                // エラー時は `now()` にフォールバック
                $receiptDate = now()->format('Y年m月d日');
            }
        } else {
            $receiptDate = now()->format('Y年m月d日');
        }

        return view('supports.receipt', compact('matching', 'receiptDate'));
    }


    // 領収書更新メソッド
    public function update(Request $request, $request_id)
    {
        // バリデーションルール
        $validated = $request->validate([
            'time' => 'required|numeric|min:0.5|max:8.0', // サポート時間
            'distance' => 'required|numeric|min:2', // 距離
            'sonotacost1' => 'nullable|numeric|min:0', // その他費用1
            'sonotacost2' => 'nullable|numeric|min:0', // その他費用2
            'sonotacost3' => 'nullable|numeric|min:0', // その他費用3
            'remarks' => 'nullable|string|max:1000', // 備考
        ]);

        // マッチングデータを取得
        $matching = Matching::where('request_id', $request_id)->firstOrFail();

        // 依頼データを取得
        $userRequest = UserRequest::findOrFail($request_id);

        // 各項目の計算
        $transportation_costs = round($validated['distance'] * 15 * 2, 2); // 交通費
        $totalCost = round(
            ($matching->cost * $validated['time']) +
            $transportation_costs +
            ($validated['sonotacost1'] ?? 0) +
            ($validated['sonotacost2'] ?? 0) +
            ($validated['sonotacost3'] ?? 0),
            2
        );

        // マッチングテーブルを更新
        $matching->update([
            'time' => $validated['time'], // サポート時間（小数対応）
            'distance' => $validated['distance'], // 距離を保存
            'transportation_costs' => $transportation_costs, // 交通費
            'sonotacost1' => $validated['sonotacost1'] ?? 0, // その他費用1
            'sonotacost2' => $validated['sonotacost2'] ?? 0, // その他費用2
            'sonotacost3' => $validated['sonotacost3'] ?? 0, // その他費用3
            'costkei' => $totalCost, // 合計金額
            'remarks' => $validated['remarks'], // 備考
            'status' => 4, // ステータス
            'syousyu_flg' => 1, // 領収済フラグ
            'closed_at' => now(), // 更新日時
        ]);

// 通知送信
$url = route('receipts.show', ['request_id' => $request_id]);
$requester = $matching->requester; // 依頼者のリレーションを取得

if ($requester) {
    $requester->notify(new ReceiptNotification(
        '領収書が発行されました。詳細はこちらをご確認ください。',
        $url
    ));
}

        // 依頼テーブルのステータスを更新
        $userRequest->update([
            'status_id' => 4, // 依頼のステータスを終了(4)に更新
        ]);

        // 成功メッセージをリダイレクト先に渡す
        return redirect()
            ->route('receipts.show', ['request_id' => $request_id])
            ->with('success', 'データが正常に更新されました');
    }

    // PDF生成メソッド
    public function generatePdf($request_id)
{
    $matching = Matching::where('request_id', $request_id)->firstOrFail();
    $userRequest = UserRequest::findOrFail($request_id);

    // `syousyu_flg` が 1 の場合は `closed_at` を使う
    if ($matching->syousyu_flg == 1 && !empty($matching->closed_at)) {
        try {
            $receiptDate = Carbon::parse($matching->closed_at)->format('Y年m月d日');
        } catch (\Exception $e) {
            $receiptDate = now()->format('Y年m月d日');
        }
    } else {
        $receiptDate = now()->format('Y年m月d日');
    }

    $pdf = Pdf::loadView('supports.pdf', compact('matching', 'receiptDate'));

    return $pdf->stream('領収書.pdf');
}

}
