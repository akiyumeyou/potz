<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\Chat;

class ChatAIController extends Controller
{
    public function aiResponse(Request $request)
{
    try {
        $request->validate([
            'message' => 'required|string|max:500',
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'error' => '認証エラー'], 401);
        }

        // **ユーザーの投稿をデータベースに保存**
        $userMessage = Chat::create([
            'user_id' => $user->id,
            'user_name' => $user->name, // ✅ ログインユーザーの名前を保存
            'content' => $request->message,
        ]);

        // **ChatGPT API にリクエスト**
        $apiKey = env('OPENAI_API_KEY');
        $response = Http::withHeaders([
            'Authorization' => "Bearer $apiKey",
            'Content-Type' => 'application/json'
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4',
            'messages' => [
                ['role' => 'system', 'content' => 'あなたは簡潔に応答するAIです。短く的確に答えてください。'],
                ['role' => 'user', 'content' => $request->message]
            ]
        ]);

        if ($response->failed()) {
            return response()->json(['success' => false, 'error' => 'APIリクエスト失敗'], 500);
        }

        $aiMessage = $response->json('choices.0.message.content', 'AI応答に失敗しました。');

        // **AIの応答をデータベースに保存**
        $aiResponse = Chat::create([
            'user_id' => 2, // AIは仮のユーザーID
            'user_name' => 'POTZ_AI', // ✅ AIの名前を固定
            'content' => $aiMessage,
        ]);

        return response()->json(['success' => true, 'message' => $aiMessage, 'chat' => $aiResponse]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}
}
