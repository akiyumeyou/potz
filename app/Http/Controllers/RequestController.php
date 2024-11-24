<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; // HTTPリクエスト
use App\Models\Userrequest; // モデル名 Userrequest を正しく参照
use App\Models\MeetRoom; // MeetRoom モデルを参照

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
        return view('requests.create');
    }

    /**
     * 依頼を保存
     */
    public function store(Request $request)
    {
        // バリデーション
        $validated = $request->validate([
            'contents' => 'required|string|max:1000',
            'date' => 'required|date',
            'time' => 'nullable|integer|min:0|max:23',
            'spot' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
        ]);

        // データを保存（status_idを1に設定）
        $newRequest = Userrequest::create([
            'contents' => $validated['contents'],
            'date' => $validated['date'],
            'time' => $validated['time'] ?? null,
            'spot' => $validated['spot'] ?? null,
            'address' => $validated['address'] ?? null,
            'requester_id' => auth()->id(),
            'status_id' => 1, // 準備中
        ]);

        // MeetRoom を作成
        MeetRoom::create([
            'request_id' => $newRequest->id, // 新規依頼のIDを関連付け
        ]);

        // 依頼一覧にリダイレクト
        return redirect()->route('index')->with('success', '依頼が登録され、チャットルームが作成されました。');
    }
}
