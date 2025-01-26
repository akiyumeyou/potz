<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'file' => 'nullable|file|mimes:pdf|max:2048', // PDFファイルのみ、2MBまで
        ]);

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('public/posts');
            $fileName = $request->file('file')->hashName(); // ファイル名を取得
        }

        $post = new Post();
        $post->title = $validated['title'];
        $post->content = $validated['content'];
        $post->file_path = $path ?? null;
        $post->save();

        return redirect()->route('admin.posts.index')->with('success', '投稿が保存されました！');
    }

     // 新しい投稿を作成するためのフォームを表示
     public function create()
     {
         return view('admin.posts.create');
     }

     public function edit(Post $post)
    {
        return view('admin.posts.edit', compact('post'));
    }
    public function update(Request $request, Post $post)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'content' => 'required|string',
    ]);

    if ($request->hasFile('file')) {
        $path = $request->file('file')->store('public/files');
        $post->file_path = $path;
    }

    $post->title = $validated['title'];
    $post->content = $validated['content'];
    $post->save();

    return redirect()->route('posts.index')->with('success', '投稿が更新されました！');
}


}
