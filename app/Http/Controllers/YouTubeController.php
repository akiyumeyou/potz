<?php
namespace App\Http\Controllers;

use App\Models\YouTube;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class YouTubeController extends Controller
{
    public function index()
{
    $videos = YouTube::with('user')
        ->orderBy('created_at', 'desc')
        ->paginate(6);
        // foreach ($videos as $v) {
        //     dump([
        //         'id' => $v->id,
        //         'user_id' => $v->user_id,
        //         'user' => $v->user ? $v->user->name : null,
        //     ]);
        // }
        // exit;
    // コレクション部分に対して through() を適用
    $videos->setCollection(
        $videos->getCollection()->map(function ($video) {
            $video-> user_name = $video->user ? $video->user->name : '匿名';
            return $video;
        })
    );

    return view('youtube.index', compact('videos'));
}


    public function store(Request $request)
    {
        $request->validate([
            'youtube_link' => 'required|url',
            'comment' => 'nullable|string',
            'category' => 'required|string',
        ]);

        $data = $request->all();
        $data['user_id'] = auth()->id();
        $data['like_count'] = 0;

        YouTube::create($data);

        return redirect()->route('youtube.index');
    }

    public function updateLikes($id)
    {
        $video = YouTube::findOrFail($id);
        $video->like_count += 1;
        $video->save();

        return redirect()->route('youtube.index');
    }

    public function destroy($id)
    {
        $video = YouTube::findOrFail($id);
        $video->delete();

        return redirect()->route('youtube.index');
    }

    public function search(Request $request)
    {
        $apiKey = config('services.youtube.api_key');
        
        // APIキーの確認
        if (empty($apiKey)) {
            return response()->json([
                'error' => 'YouTube APIキーが設定されていません',
                'debug' => 'YOUTUBE_API_KEYの設定を確認してください'
            ], 500);
        }

        $query = $request->input('query');
        $maxResults = 10;

        try {
            $url = 'https://www.googleapis.com/youtube/v3/search';
            $params = [
                'part' => 'snippet',
                'q' => $query,
                'type' => 'video',
                'maxResults' => $maxResults,
                'key' => $apiKey,
                'order' => 'relevance',
                'relevanceLanguage' => 'ja',
                'publishedAfter' => now()->subYears(2)->toIso8601String(),
            ];

            \Log::info('YouTube API Request:', [
                'url' => $url,
                'params' => array_merge($params, ['key' => '***']), // APIキーはログに表示しない
            ]);

            $response = Http::get($url, $params);

            \Log::info('YouTube API Response:', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                return response()->json($response->json());
            } else {
                return response()->json([
                    'error' => 'YouTube APIからの応答がありません',
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'debug' => 'APIキーとクォータ制限を確認してください'
                ], 500);
            }
        } catch (\Exception $e) {
            \Log::error('YouTube API Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'YouTube APIへの接続に失敗しました',
                'message' => $e->getMessage(),
                'debug' => 'APIキーとネットワーク接続を確認してください'
            ], 500);
        }
    }

    public function getVideoStats($videoId)
    {
        $apiKey = config('services.youtube.api_key');
        
        if (empty($apiKey)) {
            return response()->json([
                'error' => 'YouTube APIキーが設定されていません'
            ], 500);
        }

        try {
            $url = 'https://www.googleapis.com/youtube/v3/videos';
            $params = [
                'part' => 'statistics',
                'id' => $videoId,
                'key' => $apiKey
            ];

            $response = Http::get($url, $params);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['items'][0])) {
                    return response()->json([
                        'statistics' => $data['items'][0]['statistics']
                    ]);
                }
                return response()->json([
                    'error' => '動画の統計情報が見つかりません'
                ], 404);
            }

            return response()->json([
                'error' => 'YouTube APIからの応答がありません'
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'YouTube APIへの接続に失敗しました'
            ], 500);
        }
    }
}
