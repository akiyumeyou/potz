<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function store(Request $request)
    {
        // バリデーション
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', // PDFと画像を許可
        ]);

        // ファイル保存
        $filePath = $request->file('file')
            ? $request->file('file')->store('posts', 'public')
            : null;

        // データ保存
        Post::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'file_path' => $filePath,
        ]);

        return redirect()->route('admin.posts.index')->with('success', '投稿が保存されました！');
    }

    public function create()
    {
        return view('admin.posts.create');
    }

    public function edit(Post $post)
    {
        return view('admin.posts.edit', compact('post'));
    }

    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        // バリデーション
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'file' => 'nullable|file|mimes:pdf|max:2048', // PDFのみ許可
        ]);

        // ファイルの差し替え処理
        if ($request->hasFile('file')) {
            // 既存ファイルを削除
            if ($post->file_path && Storage::disk('public')->exists($post->file_path)) {
                Storage::disk('public')->delete($post->file_path);
            }

            // 新しいファイルを保存
            $filePath = $request->file('file')->store('posts', 'public');
            $post->file_path = $filePath;
        }

        // その他のフィールドを更新
        $post->title = $validated['title'];
        $post->content = $validated['content'];
        $post->save();

        return redirect()->route('admin.posts.index')->with('success', '投稿が更新されました！');
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);

        // ファイルが存在する場合は削除
        if ($post->file_path && Storage::disk('public')->exists($post->file_path)) {
            Storage::disk('public')->delete($post->file_path);
        }

        // 投稿を削除
        $post->delete();

        return redirect()->route('admin.posts.index')->with('success', '投稿が削除されました！');
    }

    public function download($id)
    {
        $post = Post::findOrFail($id);

        // ダウンロード処理
        return Storage::disk('public')->download($post->file_path);
    }
}
