<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; // HTTPリクエスト
use App\Models\UserRequest; // モデル名 Userrequest を正しく参照
use App\Models\MeetRoom; // MeetRoom モデルを参照
use Illuminate\Support\Facades\DB; // DBファサードを使用

class RequestController extends Controller
{
    /**
     * ダッシュボードの依頼一覧を表示
     */
    public function index()
    {
        // ログインユーザーの依頼を取得
        $requests = Userrequest::where('requester_id', auth()->id())->get();

        return view('requests.index', compact('requests'));
    }

    /**
     * 依頼作成フォームを表示
     */
    public function create()
    {
        // category3テーブルからカテゴリデータを取得
        $categories = DB::table('category3')->select('id', 'category3')->get();

        // フォーム表示
        return view('requests.create', compact('categories'));
    }

    /**
     * 依頼を保存
     */
    public function store(Request $request)
    {
        // バリデーション
        $validated = $request->validate([
            'category3_id' => 'required|exists:category3,id',
            'contents' => 'required|string|max:1000',
            'date' => 'required|date', // 日付だけを期待
            'time_start' => 'required|date_format:H:i', // 時刻部分だけを期待
            'time' => 'required|numeric|min:0.5|max:8.0',
            'spot' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
        ]);

        // 日付と時刻を結合して日時を作成
        $datetime = \Carbon\Carbon::parse($validated['date'])->format('Y-m-d') . ' ' . $validated['time_start'];

        // データを保存
        $newRequest = Userrequest::create([
            'category3_id' => $validated['category3_id'],
            'contents' => $validated['contents'],
            'date' => $datetime, // 結合した日時を保存
            'time_start' => $validated['time_start'], // 時刻のみ
            'time' => $validated['time'],
            'spot' => $validated['spot'] ?? null,
            'address' => $validated['address'] ?? null,
            'requester_id' => auth()->id(),
            'status_id' => 1, // 準備中に設定
        ]);

        // MeetRoom 作成
        MeetRoom::create([
            'request_id' => $newRequest->id,
        ]);

        return redirect()->route('requests.index')->with('success', '依頼が登録され、チャットルームが作成されました。');
    }


}
