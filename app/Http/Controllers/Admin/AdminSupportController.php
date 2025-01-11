<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\UserRequest;
use App\Models\Support;
use Illuminate\Http\Request;
use App\Models\MeetRoomMember;
use App\Models\MeetRoom;

class AdminSupportController extends Controller
{
    public function index(Request $request)
{
    $filter = $request->query('filter', 'all'); // フィルタ条件を取得
    $query = UserRequest::with(['user', 'supporter']); // 依頼者とサポーターのリレーションをロード

    // フィルタリング条件
    if ($filter === 'new') {
        $query->where('status_id', 1);
    } elseif ($filter === 'in_progress') {
        $query->whereIn('status_id', [2, 3]);
    } elseif ($filter === 'completed') {
        $query->where('status_id', 4);
    } elseif ($filter === 'cancelled') {
        $query->where('status_id', 5);
    }

    $supports = $query->paginate(10);

    return view('admin.supports.index', compact('supports'));
}



    public function edit($id)
    {
        $support = UserRequest::findOrFail($id);
        return view('admin.supports.edit', compact('support'));
    }

    public function update(Request $request, $id)
    {
        $support = UserRequest::findOrFail($id);
        $support->update($request->all());
        return redirect()->route('admin.supports.index')->with('success', 'サポート情報を更新しました。');
    }

    public function destroy($id)
    {
        $support = UserRequest::findOrFail($id);
        $support->delete();
        return redirect()->route('admin.supports.index')->with('success', 'サポート情報を削除しました。');
    }

    public function show($id)
    {
        // MeetRoom と UserRequest を取得
        $meetRoom = MeetRoom::with('members.user', 'meets.sender')->findOrFail($id);
        $userRequest = $meetRoom->userrequest;

        // チャットルームビューを表示
        return view('requests.show', compact('meetRoom', 'userRequest'));
    }

    public function meet($id)
    {
        // MeetRoomMember に管理者が登録されているか確認
        $member = MeetRoomMember::where('user_id', auth()->id())
                                ->where('role', 'コーディネーター')
                                ->where('meet_room_id', $id)
                                ->first();

        if (!$member) {
            return redirect()->route('admin.supports.index')->with('error', 'チャットルームに入る権限がありません。');
        }

        // チャットルームデータを取得
        $room = MeetRoom::find($id);

        if (!$room) {
            return redirect()->route('admin.supports.index')->with('error', '指定されたチャットルームが見つかりません。');
        }

        // チャットルームビューにデータを渡す
        return view('requests.show', compact('room'));
    }
}
