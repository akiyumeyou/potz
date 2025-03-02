<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function index()
    {
        $chats = Chat::orderBy('created_at', 'asc')->get();
        return view('chats.index', compact('chats'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'content' => 'nullable|string|max:1000',
                'image' => 'nullable|file|image|mimes:jpeg,png,jpg,gif,webp|max:8192'
            ]);

            $content = $request->content ?? '';

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('uploads', 'public');
                $content = asset("storage/" . $path);
            }

            if (empty($content)) {
                return response()->json(['error' => 'メッセージまたは画像が必要です。'], 400);
            }

            $chat = Chat::create([
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name,
                'content' => $content,
                'message_type' => $request->hasFile('image') ? 'image' : 'text',
            ]);

            return response()->json([
                'id' => $chat->id,
                'user_id' => $chat->user_id,
                'user_name' => $chat->user_name,
                'content' => $chat->content,
                'message_type' => $chat->message_type,
                'created_at' => $chat->created_at->format('Y-m-d H:i'),
                'scroll' => true
            ]);

        } catch (\Exception $e) {
            Log::error('エラー発生: ' . $e->getMessage());
            return response()->json(['error' => 'エラーが発生しました'], 500);
        }
    }

    public function getChats()
    {
        $chats = Chat::orderBy('created_at', 'asc')->get();

        return response()->json($chats->map(function ($chat) {
            return [
                'id' => $chat->id,
                'user_id' => $chat->user_id,
                'user_name' => $chat->user_name,
                'content' => $chat->content,
                'message_type' => $chat->message_type,
                'created_at' => $chat->created_at->format('Y-m-d H:i')
            ];
        }));
    }

    public function destroy($id)
    {
        try {
            $chat = Chat::findOrFail($id);

            if ($chat->user_id !== Auth::id()) {
                return response()->json(['error' => '削除権限がありません'], 403);
            }

            if ($chat->message_type === 'image') {
                $filePath = str_replace(asset('storage/'), '', $chat->content);
                Storage::disk('public')->delete($filePath);
            }

            $chat->delete();

            return response()->json(['success' => '削除しました']);
        } catch (\Exception $e) {
            return response()->json(['error' => '削除エラー'], 500);
        }
    }
}
