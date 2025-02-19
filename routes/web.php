<?php

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\SenryuController;
use App\Http\Controllers\YouTubeController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\MeetRoomController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\Category3Controller;
use App\Http\Controllers\Admin\AdminSupportController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\GoogleLoginController;
use App\Http\Controllers\SupporterProfileController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\MatchingsController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\AdminEventController;
use App\Models\Event;
use Carbon\Carbon;

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\VerifyEmailController;

// ログイン後に認証が必要なルート
Route::middleware(['auth'])->group(function () {
    // メール認証を促すページ（未認証ユーザーがアクセスした際の通知）
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    // 認証メールの再送信
    Route::post('/email/verification-notification', function (Request $request) {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard')->with('message', 'すでに認証済みです。');
        }

        $request->user()->sendEmailVerificationNotification();

        session()->flash('message', '認証メールを送信しました！');

        dd(session()->all()); // セッションの内容を確認
        return redirect()->route('dashboard');
    })->name('verification.send');

});


// Route::middleware(['auth'])->group(function () {
//     // メール認証を促すページ
//     Route::get('/email/verify', [EmailVerificationPromptController::class, '__invoke'])
//         ->name('verification.notice');

//     // 認証メールの再送信
//     Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
//         ->name('verification.send');
// });

Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/dashboard'); // ログイン中ならダッシュボードへ
    }
    return redirect('/login'); // 未ログインならログインページへ
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// 認証が必要なルート
Route::middleware('auth')->group(function () {
    // プロフィール管理
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit'); // プロフィール編集画面
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update'); // プロフィール更新
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy'); // アカウント削除
        Route::get('/profile/request-submission', [ProfileController::class, 'requestSubmission'])
             ->name('profile.request_submission');
});



Route::middleware('auth')->group(function () {
    Route::get('/supporter-profile/edit', [SupporterProfileController::class, 'edit'])->name('supporter-profile.edit');
    Route::patch('/supporter-profile', [SupporterProfileController::class, 'update'])->name('supporter-profile.update');
});




// 認証と Google ログイン
Route::get('/auth/google', [GoogleLoginController::class, 'redirectToGoogle'])->name('login.google');
Route::get('/auth/google/callback', [GoogleLoginController::class, 'handleGoogleCallback'])->name('login.google.callback');

// イベント管理
Route::middleware('auth')->group(function () {
    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
    Route::patch('/events/{event}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
    Route::post('/events/{event}/participate', [EventController::class, 'participate'])->name('events.participate');
});

// 川柳
Route::resource('senryus', SenryuController::class);
Route::post('/senryus/{id}/iine', [SenryuController::class, 'incrementIine'])->name('senryus.incrementIine');
Route::get('/senryus', [SenryuController::class, 'index'])->name('senryus.index');

//おすすめYoutube
Route::get('/youtubes', [YouTubeController::class, 'index'])->name('youtube.index');
Route::post('/youtubes', [YouTubeController::class, 'store'])->name('youtube.store');
Route::post('/youtubes/{id}/likes', [YouTubeController::class, 'updateLikes'])->name('youtube.updateLikes');
Route::delete('/youtubes/{id}', [YouTubeController::class, 'destroy'])->name('youtube.destroy');

/// 認証が必要なルート
Route::middleware(['auth'])->group(function () {
        Route::prefix('requests')->group(function () {
        Route::get('/', [RequestController::class, 'index'])->name('requests.index'); // 依頼一覧
        Route::post('/{id}/thank', [RequestController::class, 'addThank'])->name('requests.thank'); //
        Route::get('/create', [RequestController::class, 'create'])->name('requests.create'); // 新規依頼作成
// 依頼者が再依頼する
Route::get('/requests/create/{from_request}', [RequestController::class, 'createFromRequest'])->name('requests.createFromRequest');

// サポーターが再依頼を登録する
Route::get('/requests/create/{from_request}', [RequestController::class, 'createFromRequest'])->name('requests.createFromRequest');

        Route::post('/', [RequestController::class, 'store'])->name('requests.store'); // 依頼の保存
        Route::get('/{id}/edit', [RequestController::class, 'edit'])->name('requests.edit'); // 編集
        Route::put('/{id}', [RequestController::class, 'update'])->name('requests.update'); // 更新
        Route::get('/{id}', [RequestController::class, 'show'])->name('requests.show'); // 詳細
    });

    // 打ち合わせルーム
    Route::get('/meet_rooms/{request_id}', [MeetRoomController::class, 'show'])->name('meet_rooms.show');
    Route::post('/meet_rooms/{id}', [MeetRoomController::class, 'store'])->name('meet_rooms.store');
    Route::post('/meet_rooms/{room}/image', [MeetRoomController::class, 'storeImage'])->name('meet_rooms.image');

    // マッチング
    Route::get('/matchings/{id}', [MatchingsController::class, 'show'])->name('matchings.show');
    Route::post('/matchings/confirm', [MatchingsController::class, 'confirm'])->name('matchings.confirm');

    // 領収書表示（発行フォーム表示）
    Route::get('/receipts/{request_id}', [ReceiptController::class, 'show'])->name('receipts.show');
    // 領収書更新（入金処理とDB保存）
    Route::put('/receipts/{request_id}', [ReceiptController::class, 'update'])->name('receipts.update');
    // PDF生成・表示
    Route::get('/receipts/pdf/{request_id}', [ReceiptController::class, 'generatePdf'])->name('receipts.generatePdf');

    // サポーター用依頼一覧
    Route::get('/supports', [SupportController::class, 'index'])->name('supports.index'); // サポーター用依頼一覧
    Route::post('/supports/join/{id}', [SupportController::class, 'joinRoom'])->name('support.joinRoom');

});



// 管理画面用ルート
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('home');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/categories', [AdminController::class, 'categories'])->name('categories');
    Route::resource('category3', Category3Controller::class)->except(['show']);
    Route::get('/supports', [AdminController::class, 'supports'])->name('supports');
    // 管理画面からのメール送信用ルート
    Route::post('users/send_email', [AdminUserController::class, 'sendEmail'])->name('users.send_email');
// 掲示板投稿に関するルート
    Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
    Route::get('/posts/create', [AdminPostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [AdminPostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}/edit', [AdminPostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [AdminPostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [AdminPostController::class, 'destroy'])->name('posts.destroy');

    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/events', [AdminEventController::class, 'index'])->name('events.index');
    Route::get('/events/create', [AdminEventController::class, 'create'])->name('events.create');
    Route::post('/events', [AdminEventController::class, 'store'])->name('events.store');
    Route::get('/events/{event}/participants', [AdminEventController::class, 'showParticipants'])
    ->name('events.participants'); // 🔹 参加者確認ページ
    Route::post('/events/{event}/add-participant', [AdminEventController::class, 'addParticipant'])
    ->name('events.add-participant'); // 参加者追加
    // 参加者の承認・入金状態の切り替え
    Route::patch('/event-participants/{participant}/toggle-status',
        [AdminEventController::class, 'toggleStatus'])
        ->name('event-participants.toggle-status');

    Route::patch('/event-participants/{participant}/toggle-payment',
        [AdminEventController::class, 'togglePayment'])
        ->name('event-participants.toggle-payment');

});

// 一般ユーザー向けの掲示板表示用ルート
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('supports', \App\Http\Controllers\Admin\AdminSupportController::class);
    Route::get('/supports/{id}/meet', [\App\Http\Controllers\Admin\AdminSupportController::class, 'show'])->name('supports.meet');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', \App\Http\Controllers\Admin\AdminUserController::class);
    Route::post('/users/{id}/approve', [AdminUserController::class, 'approve'])->name('users.approve');
    Route::post('/users/{id}/unapprove', [AdminUserController::class, 'unapprove'])->name('users.unapprove');
});


Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');

Route::get('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');


require __DIR__.'/auth.php';

Route::get('/events/status', [EventController::class, 'status'])->name('events.status');
