<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\SupportNotification;
use App\Notifications\ConfirmNotification;
use App\Notifications\ReceiptNotification;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function sendSupportNotification(Request $request)
    {
        $user = User::find($request->input('user_id')); // 通知先ユーザー
        $supporterName = auth()->user()->name; // サポーター名
        $url = route('requests.show', ['id' => $request->input('request_id')]);

        $user->notify(new SupportNotification($supporterName, $url));

        return response()->json(['message' => 'サポート通知を送信しました。']);
    }

    public function sendConfirmNotification(Request $request)
    {
        $requestId = $request->input('request_id');
        $url = route('requests.show', ['id' => $requestId]);

        // 双方に通知
        $users = User::whereIn('id', [$request->input('requester_id'), $request->input('supporter_id')])->get();
        foreach ($users as $user) {
            $user->notify(new ConfirmNotification($url));
        }

        return response()->json(['message' => '確定通知を送信しました。']);
    }

    public function sendReceiptNotification(Request $request)
    {
        $user = User::find($request->input('user_id')); // 通知先ユーザー
        $url = route('receipts.show', ['id' => $request->input('receipt_id')]);

        $user->notify(new ReceiptNotification($url));

        return response()->json(['message' => '領収通知を送信しました。']);
    }
    
    public function markAsRead($id)
    {
        // 該当する通知を取得
        $notification = DatabaseNotification::findOrFail($id);

        // ログインユーザーに関連する通知か確認
        if ($notification->notifiable_id !== auth()->id()) {
            abort(403, '権限がありません');
        }

        // 通知を既読にする
        $notification->markAsRead();

        // リンク先にリダイレクト
        return redirect($notification->data['url'] ?? '/');
    }
}
