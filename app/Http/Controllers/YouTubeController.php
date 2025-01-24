<?php
namespace App\Http\Controllers;

use App\Models\YouTube;
use Illuminate\Http\Request;

class YouTubeController extends Controller
{
    public function index()
    {
        $videos = YouTube::all();
        return view('youtube.index', compact('videos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'youtube_link' => 'required|url',
            'comment' => 'nullable|string',
            'category' => 'required|string',
        ]);

        YouTube::create($request->all());

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
}
