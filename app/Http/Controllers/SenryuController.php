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
        return view('senryu.index', compact('senryus'));
    }

    // 新規作成フォーム表示
    public function create()
    {
        return view('senryu.create');
    }

    // 新規作成処理
    public function store(Request $request)
{
    $request->validate([
        'theme' => 'nullable|string|max:128',
        's_text1' => 'nullable|string|max:10',
        's_text2' => 'nullable|string|max:10',
        's_text3' => 'nullable|string|max:10',
        'img_path' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi|max:20480', // 20MBまでの画像または動画
    ]);

    try {
        $data = $request->except('img_path');
        $data['user_id'] = Auth::id();
        $data['user_name'] = Auth::user()->name;
        $data['iine'] = 0; // 新規作成時に iine フィールドを 0 に設定

        if ($request->hasFile('img_path')) {
            Log::info('File is present. Processing upload.');
            $filePath = $request->file('img_path')->store('senryu', 'public');

            // 保存されたファイルのフルURLを生成
            $data['img_path'] = url('/storage/' . $filePath);

            Log::info('File uploaded to: ' . $data['img_path']);
        } else {
            Log::info('No file uploaded.');
            $data['img_path'] = ''; // img_pathが提供されていない場合のデフォルト値
        }

        Senryu::create($data);
        Log::info('Senryu created successfully.');

        return redirect()->route('senryus.index')->with('success', '川柳が作成されました');
    } catch (\Exception $e) {
        Log::error('Error creating Senryu: ' . $e->getMessage());
        return redirect()->back()->with('error', 'エラーが発生しました: ' . $e->getMessage());
    }
}



    // 詳細表示
    public function show(Senryu $senryu)
    {
        return view('senryu.show', compact('senryu'));
    }

    // 編集フォーム表示
    public function edit(Senryu $senryu)
    {
        return view('senryu.edit', compact('senryu'));
    }

    // 更新処理
    public function update(Request $request, Senryu $senryu)
    {
        $request->validate([
            'theme' => 'nullable|string|max:128',
            's_text1' => 'nullable|string|max:10',
            's_text2' => 'nullable|string|max:10',
            's_text3' => 'nullable|string|max:10',
            'img_path' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi|max:20480', // 20MBまでの画像または動画
        ]);

        try {
            $data = $request->except('img_path');

            if ($request->hasFile('img_path')) {
                $filePath = '';

                if (strpos($request->file('img_path')->getMimeType(), 'image') !== false) {
                    $filePath = $request->file('img_path')->store('public/senryu');
                } else {
                    $filePath = $request->file('img_path')->store('public/senryu');
                }

                if ($senryu->img_path) {
                    Storage::delete(str_replace('/storage/', 'public/', $senryu->img_path));
                }

                $data['img_path'] = Storage::url($filePath);
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

        return redirect()->route('senryu.index');
    }


}
