<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Matching;

class ReceiptController extends Controller
{
    public function show($request_id)
    {
        // マッチングデータを取得
        $matching = Matching::where('request_id', $request_id)->firstOrFail();

        // 表示用ビューにデータを渡す
        return view('supports.receipt', compact('matching'));
    }

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

  // ログに取得したデータを記録（確認用）
  logger('Before update:', $matching->toArray());

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
        // $validated['distance'] = $request->input('distance', 0);
        // データベースを更新
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

// ログに更新後のデータを記録（確認用）
logger('After update:', $matching->toArray());

        // 成功メッセージをリダイレクト先に渡す
        return redirect()
            ->route('receipts.show', ['request_id' => $request_id])
            ->with('success', 'データが正常に更新されました');
    }
}

