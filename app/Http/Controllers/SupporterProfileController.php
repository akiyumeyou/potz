<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use App\Models\SupporterProfile;

class SupporterProfileController extends Controller
{
    protected $fillable = ['user_id', 'ac_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    /**
     * サポートプロフィール編集画面を表示
     */
    public function edit()
    {
        $user = Auth::user();
        $profile = $user->supporterProfile;

        return view('profile.supporter_profiles.edit', compact('user', 'profile'));
    }

    /**
     * サポートプロフィールを更新
     */
    public function update(Request $request)
    {
        $request->validate([
            'self_introduction' => 'nullable|string|max:1000',
            'skill1' => 'nullable|string|max:255',
            'skill2' => 'nullable|string|max:255',
            'skill3' => 'nullable|string|max:255',
            'skill4' => 'nullable|string|max:255',
            'skill5' => 'nullable|string|max:255',
            'pref_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MBまでの画像
        ]);

        $user = Auth::user();
        $profile = $user->supporterProfile;

        // プロファイルが存在しない場合は新規作成
        if (!$profile) {
            $profile = SupporterProfile::create([
                'user_id' => $user->id,
            ]);
        }

        try {
            // ファイルアップロード処理
            if ($request->hasFile('pref_photo')) {
                $file = $request->file('pref_photo');
                $path = $file->store('supports', 'public'); // 保存

                // 古い画像があれば削除
                if ($profile->pref_photo) {
                    Storage::disk('public')->delete($profile->pref_photo);
                }

                // 新しい画像のパスと認証ステータスを更新
                $profile->update([
                    'pref_photo' => $path,
                    'ac_id' => 1, // 申請中
                ]);
            }

            // 他のフィールドを更新
            $profile->update($request->only([
                'self_introduction',
                'skill1',
                'skill2',
                'skill3',
                'skill4',
                'skill5',
            ]));

            return redirect()->route('profile.edit')->with('status', 'プロフィールが更新されました。');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => '画像の保存中にエラーが発生しました: ' . $e->getMessage()]);
        }
    }


}
