<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Matching;
use App\Models\UserRequest; // 依頼テーブルモデルをインポート
use Barryvdh\DomPDF\Facade\Pdf;

class ReceiptController extends Controller
{
    // 領収書表示メソッド
    public function show($request_id)
    {
        // マッチングデータを取得
        $matching = Matching::where('request_id', $request_id)->firstOrFail();
        $userRequest = UserRequest::findOrFail($request_id);

        // 表示用ビューにデータを渡す
        return view('supports.receipt', compact('matching'));
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
        $transportation_costs = round($validated['distance'] * 15, 2); // 交通費
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
        // マッチングデータを取得
        $matching = Matching::where('request_id', $request_id)->firstOrFail();

        // PDFを生成し、フォントとサイズを設定
        $pdf = Pdf::loadView('supports.pdf', compact('matching'))
                  ->setPaper('a4', 'portrait'); // 縦A4サイズ

        // カスタムフォント設定
        $pdf->getDomPDF()->getOptions()->set('defaultFont', 'migmix');

        // PDFをストリーム表示
        return $pdf->stream('領収書_' . $request_id . '.pdf');
    }

}
