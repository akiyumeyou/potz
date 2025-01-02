<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Senryu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;

class SenryuController extends Controller
{
    // 一覧表示
    public function index()
    {
        $senryus = Senryu::all();
        return view('senryus.index', compact('senryus'));
    }

    // 新規作成フォーム表示
    public function create()
    {
        return view('senryus.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'theme' => 'nullable|string|max:128',
            's_text1' => 'nullable|string|max:10',
            's_text2' => 'nullable|string|max:10',
            's_text3' => 'nullable|string|max:10',
            'img_path' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi|max:20480', // 最大20MB
        ]);

        try {
            $data = $request->except('img_path');
            $data['user_id'] = Auth::id();
            $data['user_name'] = Auth::user()->name;
            $data['iine'] = 0;

            if ($request->hasFile('img_path')) {
                $filePath = $request->file('img_path')->store('senryus', 'public');
                $data['img_path'] = 'storage/' . $filePath;
            } else {
                $data['img_path'] = 'storage/senryus/dummy.jpg'; // ダミー画像
            }

            Senryu::create($data);

            return redirect()->route('senryus.index')->with('success', '投稿が成功しました');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'エラーが発生しました: ' . $e->getMessage());
        }
    }



    // 詳細表示
    public function show(Senryu $senryu)
    {
        return view('senryus.show', compact('senryu'));
    }

    // 編集フォーム表示
    public function edit(Senryu $senryu)
    {
        return view('senryus.edit', compact('senryu'));
    }

    // 更新処理
    public function update(Request $request, $id)
{
    $request->validate([
        'theme' => 'nullable|string|max:128',
        's_text1' => 'nullable|string|max:10',
        's_text2' => 'nullable|string|max:10',
        's_text3' => 'nullable|string|max:10',
        'img_path' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi|max:20480', // 最大20MBの画像・動画
    ]);

    try {
        $senryu = Senryu::findOrFail($id);
        $data = $request->except('img_path');

        // ファイルアップロード処理
        if ($request->hasFile('img_path')) {
            // 古いファイルの削除
            if ($senryu->img_path && file_exists(public_path($senryu->img_path))) {
                unlink(public_path($senryu->img_path));
            }

            // 新しいファイルをアップロード
            $filePath = $request->file('img_path')->store('senryus', 'public');
            $data['img_path'] = 'storage/' . $filePath; // パス形式を統一
        }

        $senryu->update($data);

        return redirect()->route('senryus.index')->with('success', '川柳が更新されました');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'エラーが発生しました: ' . $e->getMessage());
    }
}

    // 削除処理
    public function destroy(Senryu $senryu)
    {
        try {
            $senryu->delete();
            return redirect()->route('senryus.index')->with('success', '川柳が削除されました');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'エラーが発生しました: ' . $e->getMessage());
        }
    }


    public function incrementIine($id)
    {
        $senryu = Senryu::findOrFail($id);
        $senryu->iine += 1;
        $senryu->save();

        return redirect()->route('senryus.index');
    }


}
