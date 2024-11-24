<?php
namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    // イベント一覧を表示するメソッド
    public function index()
    {
        $events = Event::all();
        return view('events.index', compact('events'));
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
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('events', 'public');
        }

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
        ]);

        return redirect()->route('events.index')->with('success', 'イベントが作成されました。');
    }

    // イベント編集ページを表示するメソッド
    public function edit(Event $event)
    {
        // 作成者以外のユーザーが編集ページにアクセスしようとした場合はリダイレクト
        if (Auth::id() !== $event->user_id) {
            return redirect()->route('events.index')->with('error', '編集権限がありません。');
        }

        return view('events.edit', compact('event'));
    }

    // イベントを更新するメソッド
    public function update(Request $request, Event $event)
    {
        if (Auth::id() !== $event->user_id) {
            return redirect()->route('events.index')->with('error', '更新権限がありません。');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'event_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'content' => 'required|string',
            'zoom_url' => 'required|url',
            'recurring_type' => 'required|in:once,weekly,biweekly,monthly',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $imagePath = $event->image_path;

        if ($request->hasFile('image')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('events', 'public');
        }

        $event->update([
            'title' => $request->title,
            'event_date' => $request->event_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'content' => $request->content,
            'zoom_url' => $request->zoom_url,
            'recurring' => $request->recurring_type !== 'once' ? 1 : 0,
            'recurring_type' => $request->recurring_type,
            'image_path' => $imagePath,
            'holiday' => $request->has('holiday') ? 1 : 0, // 編集時に次回休みフラグを更新
        ]);

        return redirect()->route('events.index')->with('success', 'イベントが更新されました。');
    }

    // イベントを削除するメソッド
    public function destroy(Event $event)
    {
        if (Auth::id() !== $event->user_id) {
            return redirect()->route('events.index')->with('error', '削除権限がありません。');
        }

        // イメージファイルが存在する場合は削除
        if ($event->image_path) {
            Storage::disk('public')->delete($event->image_path);
        }

        $event->delete();

        return redirect()->route('events.index')->with('success', 'イベントが削除されました。');
    }
}
