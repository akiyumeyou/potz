<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\MembershipClass;

class ProfileController extends Controller
{
    // プロフィール編集画面
    public function edit()
    {
        $user = Auth::user(); // ログイン中のユーザー
        $membership_classes = MembershipClass::all(); // 会員区分を取得

        return view('profile.edit', compact('user', 'membership_classes'));
    }

    // プロフィール更新
    public function update(Request $request)
    {
         // 基本のバリデーションルール
    $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email,' . Auth::id(),
        'membership_id' => 'required|exists:membership_classes,id',
    ];

    // 会員区分が「2以上」の場合、追加の必須チェックを設定
    if ($request->input('membership_id') >= 2) {
        $rules = array_merge($rules, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . Auth::id(),
            'membership_id' => 'required|exists:membership_classes,id',
            'gender' => 'required|in:male,female,other',
            'real_name' => 'nullable|string|max:255',
            'real_name_kana' => 'nullable|string|max:255',
            'prefecture'=> 'nullable|string|max:255',
            'address1' => 'nullable|string|max:255',
            'address2' => 'nullable|string|max:255',
            'tel' => 'nullable|string|max:20',
            'birthday' => 'nullable|date',
        ]);
    }
    // バリデーション実行
    $request->validate($rules);

        $user = Auth::user();
        $user->update($request->only([
            'name',
            'email',
            'real_name',
            'real_name_kana',
            'prefecture',
            'address1',
            'address2',
            'tel',
            'birthday',
            'membership_id',
            'gender',
        ]));

    // サポート会員 (membership_id = 3) の場合、自動作成
    if ($user->membership_id == 3 && !$user->supporterProfile) {
        $user->supporterProfile()->create();
    }

        return redirect()->route('profile.edit')->with('status', 'profile-updated');
    }



    public function requestSubmission()
    {
        $user = Auth::user();

        // 必要に応じて申請の処理を追加（例: ステータス変更や通知）

        return redirect()->route('profile.edit')->with('status', 'request-submitted');
    }
}


