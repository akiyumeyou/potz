<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MembershipClass;
use App\Models\UserProfile;

class PotzMembershipController extends Controller
{
    // 会員情報の編集フォームを表示
    public function edit()
    {
        $user = auth()->user(); // ログイン中のユーザー
        $profile = UserProfile::where('user_id', $user->id)->first(); // user_profiles テーブルから取得

        if (!$profile) {
            // プロフィールが存在しない場合、新規作成
            $profile = UserProfile::create([
                'user_id' => $user->id,
            ]);
        }

        $membership_classes = MembershipClass::all(); // 全ての会員区分を取得

        // $membership_classes をビューに渡す
        return view('profile.potzs.member', compact('profile', 'membership_classes'));
    }

    // 会員情報の更新処理
    public function update(Request $request)
    {
        $request->validate([
            'membership_id' => 'required|exists:membership_classes,id',
            'name' => 'required|string|max:255',
            'name_kana' => 'required|string|max:255',
            'post' => 'required|string|max:8',
            'address' => 'required|string|max:255',
            'tel' => 'required|string|max:15',
            'birthday' => 'required|date',
        ]);

        $profile = UserProfile::where('user_id', auth()->id())->first();
        if (!$profile) {
            return redirect()->back()->withErrors('プロフィールが見つかりません。');
        }

        $profile->update([
            'membership_id' => $request->input('membership_id'),
            'name' => $request->input('name'),
            'name_kana' => $request->input('name_kana'),
            'post' => $request->input('post'),
            'address' => $request->input('address'),
            'tel' => $request->input('tel'),
            'birthday' => $request->input('birthday'),
        ]);

        return redirect()->route('profile.edit')->with('success', '会員情報を更新しました。');
    }
}
