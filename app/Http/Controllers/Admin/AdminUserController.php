<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\SupporterProfile;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminNotificationEmail;
use Illuminate\Support\Facades\Http;

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

// メール送信処理
public function sendEmail(Request $request)
{
    // 選択されたユーザーが存在することをバリデート
    $request->validate([
        'selected_users' => 'required|array',
    ]);

    $userIds = $request->input('selected_users');
    $users = User::whereIn('id', $userIds)->get();

    // 各ユーザーへメール送信（キューを利用）
    foreach ($users as $user) {
        Mail::to($user->email)->queue(new AdminNotificationEmail($user));
    }

    return redirect()->route('admin.users.index')->with('success', 'メール送信を開始しました。');
}
public function updateCoordinates($id)
{
    try {
        $supporterProfile = SupporterProfile::findOrFail($id);
        $user = $supporterProfile->user;

        // 住所を結合
        $address = implode(' ', array_filter([
            $user->prefecture,
            $user->address1,
            $user->address2
        ]));

        // 緯度経度を取得
        [$latitude, $longitude] = $this->getCoordinates($address); // ✅ getCoordinates() を呼び出す

        if ($latitude === null || $longitude === null) {
            return redirect()->back()->with('error', '緯度経度を取得できませんでした。');
        }

        // 緯度経度を更新
        $supporterProfile->update([
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);

        return redirect()->back()->with('success', '緯度経度が更新されました。');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'エラー: ' . $e->getMessage());
    }
}


public function getCoordinates($address)
{
    $apiKey = env('GOOGLE_MAPS_API_KEY'); // `.env` に Google Maps API キーを設定する
    $url = "https://maps.googleapis.com/maps/api/geocode/json";

    $response = Http::get($url, [
        'address' => $address,
        'key' => $apiKey,
    ]);

    $data = $response->json();

    if (!empty($data['results'])) {
        $geometry = $data['results'][0]['geometry']['location'];
        return [$geometry['lat'], $geometry['lng']];
    }

    return [null, null];
}


}
