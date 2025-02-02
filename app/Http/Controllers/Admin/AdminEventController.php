<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\EventParticipant;


class AdminEventController extends Controller {

    public function index()
    {
        $paidEvents = Event::where('is_paid', 1)
            ->withCount('participants') // 参加者数を取得
            ->orderBy('event_date', 'asc')
            ->paginate(10);

        return view('admin.events.index', compact('paidEvents'));
    }

    public function create() {
        return view('admin.events.create');
    }

    public function store(Request $request) {
        Log::info("リクエストデータ: " . json_encode($request->all()));

        try {
            // バリデーション
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'event_date' => 'required|date',
                'start_time' => 'required',
                'end_time' => 'required',
                'content' => 'required|string',
                'zoom_url' => 'nullable|url',
                'recurring_type' => 'required|in:once,weekly,biweekly,monthly',
                'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'price' => 'nullable|numeric|min:0',
            ]);

            // 画像保存処理
            $imagePath = null;
            if ($request->hasFile('image')) {
                Log::info("画像アップロード開始");
                $imagePath = $request->file('image')->store('events', 'public');
                Log::info("画像保存成功: $imagePath");
            } else {
                Log::info("画像なし");
            }

            // データ保存
            $event = Event::create([
                'title' => $validated['title'],
                'event_date' => $validated['event_date'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'content' => $validated['content'],
                'zoom_url' => $validated['zoom_url'] ?? null,
                'recurring' => $request->recurring_type !== 'once' ? 1 : 0,
                'holiday' => 0,
                'recurring_type' => $validated['recurring_type'],
                'image_path' => $imagePath,
                'user_id' => Auth::id(),
                'price' => $validated['price'] ?? 0,
                'is_paid' => 1, // 🔹 有料イベントなので 1 に固定
            ]);

            Log::info("イベント保存成功: " . json_encode($event));

            return redirect()->route('events.index')->with('success', 'イベントが作成されました。');
        } catch (\Exception $e) {
            Log::error("イベント保存失敗: " . $e->getMessage());
            return back()->with('error', 'イベントの作成に失敗しました。');
        }
    }

// 📌 参加者一覧ページ
public function showParticipants(Event $event)
{
    $participants = $event->participants()->with('user')->get(); // 参加者情報

    // 🔹 Userモデルのリレーションがあることを保証
    $users = User::whereNotIn('id', $participants->pluck('user_id'))->get();

    return view('admin.events.participants', compact('event', 'participants', 'users'));
}


// 📌 管理者が参加者を追加
public function addParticipant(Request $request, Event $event)
{
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'payment_method' => 'required|string'
    ]);

    // すでに参加していないかチェック
    $exists = EventParticipant::where('event_id', $event->id)
                              ->where('user_id', $request->user_id)
                              ->exists();

    if (!$exists) {
        EventParticipant::create([
            'event_id' => $event->id,
            'user_id' => $request->user_id,
            'payment_method' => $request->payment_method,
            'status' => 1 ,// 追加時点で承認済みにする
            'amount_paid' => $event->price, // イベントの参加費をセット
        ]);
    }

    return back()->with('success', '参加者を追加しました');
}

public function toggleStatus(EventParticipant $participant)
{
    $participant->status = $participant->status === 0 ? 1 : 0;
    $participant->save();

    return redirect()->back()->with('success', '参加承認の状態を更新しました。');
}

public function togglePayment(EventParticipant $participant, Request $request)
{
    if ($participant->payment_status === 0) {
        // 入金処理：リクエストで送られた金額を保存
        $participant->payment_status = 1;
        $participant->amount_paid = $request->input('amount_paid', 0); // 未設定なら0
        $participant->save();
    } else {
        // 未入金に戻す
        $participant->payment_status = 0;
        // $participant->amount_paid = 0; // 金額をリセット
        $participant->save();
    }

    return redirect()->back()->with('success', '入金状態を更新しました');
}



}

