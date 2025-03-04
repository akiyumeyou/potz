<?php

namespace App\Http\Controllers;

use App\Models\Meet;
use App\Models\MeetRoom;
use App\Models\MeetRoomMember;
use App\Models\SupporterProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\UserRequest;

class MeetRoomController extends Controller
{

    /**
     * チャットルームを表示
     */
    public function show($requestId)
    {
        // リクエストIDからMeetRoomを取得
        $meetRoom = MeetRoom::where('request_id', $requestId)->firstOrFail();

        // 現在ログイン中のユーザーIDを取得
        $userId = Auth::id();

        // 該当ユーザーのMeetRoomMember情報を取得
        $member = MeetRoomMember::where('meet_room_id', $meetRoom->id)
            ->where('user_id', $userId)
            ->first();

        if ($member) {
            // 最新のメッセージIDを取得
            $lastMessageId = Meet::where('meet_room_id', $meetRoom->id)
                ->latest('id')
                ->value('id');

            // last_read_meet_id を更新
            if ($lastMessageId) {
                $member->last_read_meet_id = $lastMessageId; // モデルに値をセット
                $member->save(); // 保存処理
            }
        }

        // リクエスト情報を取得
        $userRequest = UserRequest::findOrFail($requestId);

        // ビューにデータを渡す
        return view('requests.show', compact('meetRoom', 'userRequest'));
    }


    // メッセージを投稿
    public function store(Request $request, $roomId)
    {
        $request->validate([
            'message' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,png,jpeg,gif|max:5120',
        ]);

        if (empty($request->message) && !$request->hasFile('image')) {
            return redirect()->back()->withErrors(['message' => 'メッセージまたは画像を入力してください']);
        }

        $meetRoom = MeetRoom::findOrFail($roomId);

        // 保存ディレクトリとファイルパスを設定
        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $directory = "meet_rooms/{$meetRoom->request_id}";

            if (!Storage::exists("public/{$directory}")) {
                Storage::makeDirectory("public/{$directory}");
            }

            $filename = time() . '.' . $image->getClientOriginalExtension();
            $imagePath = "{$directory}/{$filename}";
            $image->storeAs("public/{$directory}", $filename);
        }

        $meetRoom->meets()->create([
            'message' => $request->input('message', ''), // 空の場合は空文字列を保存
            'image' => $imagePath,
            'sender_id' => Auth::id(),
        ]);

        return redirect()->back()->with('success', '送信しました');
    }



    // ルーム一覧を表示
    public function index()
    {
        $user = Auth::user(); // 現在ログインしているユーザーを取得

        // ユーザーがログインしていない場合
        if (!$user) {
            return redirect()->route('login')->with('error', 'ログインしてください。');
        }

        // 会員区分がサポーター（membership_id == 3）でない場合はリダイレクト
        if ($user->membership_id != 3) {
            return redirect()->route('home')->with('error', 'サポーター区分ではありません。');
        }

        // supporter_profiles テーブルを参照し、認証状態を確認
        $supporterProfile = SupporterProfile::where('user_id', Auth::id())->first();

        if (!$supporterProfile || $supporterProfile->ac_id != 2) {
            return redirect()->route('home')->with('error', '認証が完了していません。');
        }

        // ビューにデータを渡す
        return view('requests.index', compact('meetRooms', 'user'));
    }

    public function storeImage(Request $request, $roomId)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,png,jpeg,gif|max:5120',
        ]);

        // ミートルームのリクエスト ID を取得
        $meetRoom = MeetRoom::findOrFail($roomId);
        $requestId = $meetRoom->request_id; // ここで request_id を取得

        // フォルダ名としてリクエスト ID を使用
        $roomFolder = "meet_rooms/{$requestId}";

        // 保存先ディレクトリのパスを取得
        $directoryPath = storage_path("app/public/{$roomFolder}");

        // ディレクトリが存在しない場合は作成
        if (!file_exists($directoryPath)) {
            mkdir($directoryPath, 0755, true);
        }

        // 画像のリサイズと保存
        $image = $request->file('image');
        $filename = time() . '.' . $image->getClientOriginalExtension();
        $path = "{$directoryPath}/{$filename}";

        list($width, $height) = getimagesize($image);

        // リサイズ処理（幅800pxに制限）
        $newWidth = 800;
        $newHeight = ($height / $width) * $newWidth;

        $sourceImage = null;
        if ($image->getClientOriginalExtension() === 'png') {
            $sourceImage = imagecreatefrompng($image);
        } else {
            $sourceImage = imagecreatefromjpeg($image);
        }

        $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($resizedImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        // 保存処理
        if ($image->getClientOriginalExtension() === 'png') {
            imagepng($resizedImage, $path);
        } else {
            imagejpeg($resizedImage, $path, 90); // 90はJPEG圧縮率
        }

        imagedestroy($sourceImage);
        imagedestroy($resizedImage);

        // データベースに保存
        $meetRoom->meets()->create([
            'message' => null,
            'image' => "{$roomFolder}/{$filename}",
            'sender_id' => Auth::id(),
        ]);

        return redirect()->back()->with('success', '画像を送信しました');
    }
    public function updateReadStatus(Request $request, $roomId)
    {
        $userId = Auth::id(); // 現在のログインユーザーID
        $meetRoomMember = MeetRoomMember::where('meet_room_id', $roomId)
            ->where('user_id', $userId)
            ->firstOrFail();

        // 最新のメッセージIDを取得
        $lastMessageId = Meet::where('meet_room_id', $roomId)
            ->latest('id')
            ->value('id');

        // last_read_meet_id を更新
        $meetRoomMember->update(['last_read_meet_id' => $lastMessageId]);

        return redirect()->back()->with('success', '既読を更新しました');
    }


}
