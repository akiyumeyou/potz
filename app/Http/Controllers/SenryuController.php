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
        $senryus = Senryu::orderBy('created_at', 'desc')->paginate(6); // 6件ごとにページネーション
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

        if ($request->has('generated_image_name') && !empty($request->input('generated_image_name'))) {
            $tempPath = storage_path('app/public/tmp/' . $request->input('generated_image_name'));
            $newFileName = 'senryus/generated_' . time() . '.jpg';
            $storagePath = storage_path('app/public/' . $newFileName);

            if (file_exists($tempPath)) {
                rename($tempPath, $storagePath); // 画像を正式保存
                $data['img_path'] = 'storage/' . $newFileName;
            } else {
                // \Log::warning("⚠️ 一時画像が見つかりません", ['tempPath' => $tempPath]);
                $data['img_path'] = 'storage/senryus/dummy.jpg';
            }
        } elseif ($request->hasFile('img_path')) {
            $filePath = $request->file('img_path')->store('senryus', 'public');
            $data['img_path'] = 'storage/' . $filePath;
        } else {
            $data['img_path'] = 'storage/senryus/dummy.jpg';
        }

        Senryu::create($data);

        return redirect()->route('senryus.index')->with('success', '投稿が成功しました');
    } catch (\Exception $e) {
        // \Log::error("🔥 投稿処理でエラー発生", ['exception' => $e->getMessage()]);
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
        if (Auth::id() !== $senryu->user_id && Auth::user()->membership_id !== 5) {
            return redirect()->route('senryus.index')->with('error', '編集権限がありません');
        }

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

        // 投稿者または管理者のみ編集可能
        if (Auth::id() !== $senryu->user_id && Auth::user()->membership_id !== 5) {
            return redirect()->route('senryus.index')->with('error', '編集権限がありません');
        }

        $data = $request->except('img_path');

        // ファイルアップロード処理
        if ($request->hasFile('img_path')) {
            // 古いファイルの削除
            if (
                $senryu->img_path
                && basename($senryu->img_path) !== 'dummy.jpg' // dummy.jpg なら削除しない
                && file_exists(public_path($senryu->img_path))
            ) {
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
            // 投稿者または管理者のみ削除可能
            if (Auth::id() !== $senryu->user_id && Auth::user()->membership_id !== 5) {
                return redirect()->route('senryus.index')->with('error', '削除権限がありません');
            }
            if ($senryu->img_path && basename($senryu->img_path) !== 'dummy.jpg' && file_exists(public_path($senryu->img_path))) {
                unlink(public_path($senryu->img_path)); // 画像ファイルを削除
            }

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
