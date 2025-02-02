<?php
namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\EventParticipant;
use Carbon\Carbon;

use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    public function index()
    {
        $today = now()->toDateString(); // 今日の日付（例: "2025-01-31"）

        // 今日のイベント（今日の日付のみ）
        $todayEvents = Event::whereDate('event_date', $today)
            ->orderBy('start_time', 'asc') // 開始時間順
            ->get();

        // 未来のイベント（今日より後）
        $futureEvents = Event::whereDate('event_date', '>', $today)
            ->orderBy('event_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->paginate(6); // 6件ごとにページネーション

        return view('events.index', compact('todayEvents', 'futureEvents'));
    }


    // イベント作成ページを表示するメソッド
    public function create()
    {
        return view('events.create');
    }

    // イベントを保存するメソッド
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'event_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'content' => 'required|string',
            'zoom_url' => 'required|url',
            'recurring_type' => 'required|in:once,weekly,biweekly,monthly',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'price' => 'nullable|numeric|min:0',
        ]);

        $imagePath = $request->file('image') ? $request->file('image')->store('events', 'public') : null;

        Event::create([
            'title' => $request->title,
            'event_date' => $request->event_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'content' => $request->content,
            'zoom_url' => $request->zoom_url,
            'recurring' => $request->recurring_type !== 'once' ? 1 : 0,
            'holiday' => 0,
            'recurring_type' => $request->recurring_type,
            'user_id' => Auth::id(),
            'image_path' => $imagePath,
            'price' => 0,
            'is_paid' => 0,

        ]);

        return redirect()->route('events.index')->with('success', 'イベントが作成されました。');
    }

    // イベント編集ページを表示するメソッド
    public function edit(Event $event)
    {
        // 作成者または管理者(membership_id == 5)のみ編集可能
        if (Auth::id() !== $event->user_id && Auth::user()->membership_id !== 5) {
            return redirect()->route('events.index')->with('error', '編集権限がありません。');
        }

        return view('events.edit', compact('event'));
    }

    // イベントを更新するメソッド
    public function update(Request $request, Event $event)
    {
        if (Auth::id() !== $event->user_id && Auth::user()->membership_id !== 5) {
            return redirect()->route('events.index')->with('error', '更新権限がありません。');
        }

        // \Log::info('イベント更新処理が呼ばれました', ['data' => $request->all()]);

        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'event_date' => 'required|date',
                'start_time' => 'required|date_format:H:i:s', // 修正
                'end_time' => 'required|date_format:H:i:s', // 修正
                'content' => 'required|string',
                'zoom_url' => 'required|url',
                'recurring_type' => 'required|in:once,weekly,biweekly,monthly',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            // \Log::info('バリデーション通過後のデータ', $validated);

            $imagePath = $event->image_path;

            if ($request->hasFile('image')) {
                if ($imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }
                $imagePath = $request->file('image')->store('events', 'public');
            }

            $updated = $event->update([
                'title' => $request->title,
                'event_date' => $request->event_date,
                'start_time' => Carbon::parse($request->start_time)->format('H:i'), // H:i に変換
                'end_time' => Carbon::parse($request->end_time)->format('H:i'), // H:i に変換
                'content' => $request->content,
                'zoom_url' => $request->zoom_url,
                'recurring' => $request->recurring_type !== 'once' ? 1 : 0,
                'recurring_type' => $request->recurring_type,
                'image_path' => $imagePath,
                'holiday' => $request->has('holiday') ? 1 : 0,
            ]);

            return redirect()->route('events.index')->with('success', 'イベントが更新されました。');

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('バリデーションエラー', ['errors' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
    }


    // イベントを削除するメソッド
    public function destroy(Event $event)
    {
     // 作成者または管理者(membership_id == 5)のみ削除可能
     if (Auth::id() !== $event->user_id && Auth::user()->membership_id !== 5) {
        return redirect()->route('events.index')->with('error', '削除権限がありません。');
    }

        // イメージファイルが存在する場合は削除
        if ($event->image_path) {
            Storage::disk('public')->delete($event->image_path);
        }

        $event->delete();

        return redirect()->route('events.index')->with('success', 'イベントが削除されました。');
    }
    public function participate(Request $request, Event $event)
    {
        $request->validate([
            'payment_method' => 'required|in:銀行振込,PayPay,その他',
        ]);

        // すでに参加予約済みか確認
        if (EventParticipant::where('event_id', $event->id)->where('user_id', Auth::id())->exists()) {
            return response()->json(['success' => false, 'error' => 'すでに参加予約済みです。']);
        }

        // 参加予約を保存
        EventParticipant::create([
            'event_id' => $event->id,
            'user_id' => Auth::id(),
            'payment_method' => $request->payment_method,
            'status' => 0, // 未承認
            'payment_status' => 0, // 未払いで登録
            'amount_paid' => $event->price, // イベントの参加費をセット
        ]);

        return response()->json(['success' => true]);
    }


}
