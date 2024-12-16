<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\SupporterProfile;

class AdminUserController extends Controller
{
    // ユーザー一覧
    public function index()
    {
        $users = User::paginate(20); // ページネーション付きで取得
        return view('admin.users.index', compact('users'));
    }

    // ユーザー詳細
    public function show($id)
{
    // ユーザー情報を取得
    $user = User::with('membershipClass')->findOrFail($id);

    // サポーター情報を取得（該当ユーザーがサポーターの場合のみ）
    $supporterProfile = $user->membership_id == 3
        ? $user->supporterProfile
        : null;

    // ビューにデータを渡す
    return view('admin.users.show', compact('user', 'supporterProfile'));
}



    // ユーザー編集フォーム
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    // ユーザー更新
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update($request->all());
        return redirect()->route('admin.users.index')->with('success', 'ユーザー情報を更新しました。');
    }

    // ユーザー削除
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'ユーザーを削除しました。');
    }

    public function approve($id)
{
    $supporterProfile = SupporterProfile::findOrFail($id);

    // 承認処理
    $supporterProfile->update(['ac_id' => 2]);

    return redirect()->back()->with('success', 'サポーターを承認しました。');
}

public function unapprove($id)
{
    $supporterProfile = SupporterProfile::findOrFail($id);

    // 承認解除処理
    $supporterProfile->update(['ac_id' => 5]);

    return redirect()->back()->with('success', 'サポーターの承認を解除しました。');
}

}
