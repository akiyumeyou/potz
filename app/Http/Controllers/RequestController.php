<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserRequest;
use App\Models\MeetRoom;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class RequestController extends Controller
{
    /**
     * ダッシュボードの依頼一覧を表示
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'ログインしてください。');
        }

        $requests = UserRequest::where('requester_id', $user->id)->get();

        return view('requests.index', compact('requests', 'user'));
    }

    /**
     * 依頼作成フォームを表示
     */
    public function create()
    {
        $categories = DB::table('category3')->select('id', 'category3', 'cost')->get();

        return view('requests.create', compact('categories'));
    }

    /**
     * 依頼を保存
     */
    public function store(Request $request)
    {
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

            $cost = DB::table('category3')->where('id', $validated['category3_id'])->value('cost');
            if (!$cost) {
                return back()->withErrors(['category3_id' => 'カテゴリに単価が設定されていません。']);
            }

            $estimate = ($cost * $validated['time']) + 400;

            $datetime = \Carbon\Carbon::parse($validated['date'])->format('Y-m-d') . ' ' . $validated['time_start'];

            $newRequest = UserRequest::create([
                'category3_id' => $validated['category3_id'],
                'contents' => $validated['contents'],
                'date' => $datetime,
                'time_start' => $validated['time_start'],
                'time' => $validated['time'],
                'spot' => $validated['spot'] ?? null,
                'address' => $validated['spot'] === 'その他' ? $validated['address'] : null,
                'parking' => $validated['parking'],
                'cost' => $cost,
                'estimate' => $estimate,
                'requester_id' => auth()->id(),
                'status_id' => 1,
            ]);

            // MeetRoom 作成
            MeetRoom::create([
                'request_id' => $newRequest->id,
            ]);

            return redirect()->route('requests.index')->with('success', '依頼が登録されました。');
        } catch (Exception $e) {
            Log::error('依頼の保存中にエラーが発生しました: ' . $e->getMessage());
            return back()->with('error', '依頼の保存に失敗しました。もう一度お試しください。');
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

            // 更新処理
            $userRequest->contents = $validated['contents'];
            $userRequest->date = $validated['date'];
            $userRequest->time_start = $validated['time_start'];
            $userRequest->time = $validated['time'];
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

        return view('requests.show', compact('meetRoom', 'userRequest'));
    }
}
