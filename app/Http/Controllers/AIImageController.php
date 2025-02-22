<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AIImageController extends Controller
{
    public function generateImage(Request $request)
    {
        try {
            // Log::info("AI 画像生成リクエスト受信");
            // Log::info("受信データ: " . json_encode($request->all()));

            // **データ取得**
            $theme = $request->input('theme');
            $s_text1 = $request->input('s_text1');
            $s_text2 = $request->input('s_text2');
            $s_text3 = $request->input('s_text3');
            $userComment = $request->input('userComment');

            // **翻訳APIで翻訳**
            $translatedTheme = $this->translateText($theme);
            $translatedPoem = $this->translateText("{$s_text1} {$s_text2} {$s_text3}");
            $translatedComment = $this->translateText($userComment);

            // **プロンプト作成**
            $prompt = "Create an artistic image inspired by traditional Japanese Senryu poetry.
                This image should visually represent the essence of the poem and reflect Japanese cultural aesthetics.
                Theme: {$translatedTheme}.
                Poem: {$translatedPoem}.
                Additional context: {$translatedComment}.
                Imagine a bright and hopeful future, using soft and warm colors.";

            // Log::info("生成プロンプト: " . $prompt);

            // **画像生成APIの設定**
            $apiKey = env('STABILITY_API_KEY');
            $endpoint = "https://api.stability.ai/v2beta/stable-image/generate/core";

            // **APIリクエスト**
            $response = Http::withHeaders([
                "Authorization" => "Bearer $apiKey",
                "Accept" => "image/*"
            ])->asMultipart()->post($endpoint, [
                'prompt' => $prompt,
                'output_format' => 'jpeg',
                'width' => 768,
                'height' => 768,
            ]);

            // **レスポンスのエラーチェック**
            if ($response->failed()) {
                Log::error("画像生成APIエラー: " . $response->body());
                return response()->json(['error' => '画像生成APIからエラーが返されました'], 500);
            }

            // **画像データ取得**
            $imageData = $response->body();

            if (empty($imageData)) {
                Log::error("⚠️ 画像データが空です");
                return response()->json(['error' => '画像データが取得できませんでした'], 500);
            }

           // **ディレクトリがなければ作成**
            $tmpDir = public_path("storage/tmp/");
            if (!is_dir($tmpDir)) {
                mkdir($tmpDir, 0775, true);
                chmod($tmpDir, 0775);
            }

            // **一時保存ファイル名**
            $uniqueId = uniqid();
            $tempFileName = "generated_{$uniqueId}.jpg";
            $tempPath = $tmpDir . $tempFileName;

            // **画像を一時保存**
            if (!file_put_contents($tempPath, $imageData)) {
                return response()->json(['error' => '画像の保存に失敗しました'], 500);
            }

            // **Webで表示可能なURLを返す**
            return response()->json([
                'image_url' => asset("storage/tmp/{$tempFileName}"),
                'image_name' => $tempFileName // 投稿時に利用する
            ]);

        } catch (\Exception $e) {
            Log::error("画像生成処理でエラー発生", ['exception' => $e->getMessage()]);
            return response()->json(['error' => '画像生成に失敗しました: ' . $e->getMessage()], 500);
        }
    }


// **翻訳メソッドを追加**
private function translateText($text)
{
    if (empty(trim($text))) {
        Log::warning("⚠️ 翻訳スキップ（空のテキスト）");
        return 'No translation available'; // **エラー時に空白でなくデフォルトの値を返す**
    }

    // **翻訳リクエストのログ**
    Log::info("翻訳リクエスト送信: " . $text);

    $response = Http::get("https://api.mymemory.translated.net/get", [
        'q' => $text,
        'langpair' => 'ja|en'
    ]);

    // Log::info("翻訳APIレスポンス: " . json_encode($response->json()));

    if ($response->successful() && isset($response['responseData']['translatedText'])) {
        return $response['responseData']['translatedText'];
    }

    Log::error("翻訳エラー: APIから適切なレスポンスが得られませんでした");
    return "Translation error"; // **エラー時にエラーを明示する**
}

}
