<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
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


Route::get('/', function () {
    return view('welcome');
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
});

/// 認証が必要なルート
Route::middleware(['auth'])->group(function () {
    // リクエスト管理
    Route::prefix('requests')->group(function () {
        Route::get('/', [RequestController::class, 'index'])->name('requests.index'); // 依頼一覧
        Route::get('/create', [RequestController::class, 'create'])->name('requests.create'); // 新規依頼作成
        Route::get('/create/{from_request}', [RequestController::class, 'createFromRequest'])->name('requests.createFromRequest'); // 再依頼
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

    // 領収書
    // 領収書表示（依頼者用: 参照専用）
    Route::get('/receipts/pdf/{request_id}', [ReceiptController::class, 'generatePdf'])->name('receipts.generatePdf');

    // 領収書発行（サポートさん用: 発行用）
    Route::get('/receipts/generate/{request_id}', [ReceiptController::class, 'generate'])->name('receipts.generate');

    // 領収書更新（入金処理）
    Route::put('/receipts/{request_id}', [ReceiptController::class, 'update'])->name('receipts.update');

    // 領収書詳細表示
    Route::get('/receipts/{request_id}', [ReceiptController::class, 'show'])->name('receipts.show');

    
// サポーター用依頼一覧
    Route::get('/supports', [SupportController::class, 'index'])->name('supports.index'); // サポーター用依頼一覧
    Route::post('/supports/join/{requestId}', [SupportController::class, 'joinRoom'])->name('supports.joinRoom');

});



// 管理画面用ルート
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('home');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/categories', [AdminController::class, 'categories'])->name('categories');
    Route::resource('category3', Category3Controller::class)->except(['show']);
    Route::get('/supports', [AdminController::class, 'supports'])->name('supports');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('supports', \App\Http\Controllers\Admin\AdminSupportController::class);
    Route::get('/supports/{id}/meet', [\App\Http\Controllers\Admin\AdminSupportController::class, 'show'])->name('supports.meet');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', \App\Http\Controllers\Admin\AdminUserController::class);
    Route::post('/users/{id}/approve', [AdminUserController::class, 'approve'])->name('users.approve');
    Route::post('/users/{id}/unapprove', [AdminUserController::class, 'unapprove'])->name('users.unapprove');
});

require __DIR__.'/auth.php';
