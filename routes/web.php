<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\MeetRoomController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\Category3Controller;
use App\Http\Controllers\GoogleLoginController;
use App\Http\Controllers\SupporterProfileController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\MatchingsController;



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

// 認証が必要なルート
Route::middleware(['auth'])->group(function () {
    // リクエスト管理
    Route::get('/requests/create', [RequestController::class, 'create'])->name('requests.create');
    Route::get('/requests/index', [RequestController::class, 'index'])->name('requests.index');
    Route::post('/requests', [RequestController::class, 'store'])->name('requests.store');
    Route::get('/requests/{id}', [RequestController::class, 'show'])->name('requests.show'); // 詳細表示
    Route::get('/requests/{id}/edit', [RequestController::class, 'edit'])->name('requests.edit');
    Route::put('/requests/{id}', [RequestController::class, 'update'])->name('requests.update');

    // 打ち合わせルーム
    Route::get('/meet_rooms/{request_id}', [MeetRoomController::class, 'show'])->name('meet_rooms.show');
    Route::post('/meet_rooms/{id}', [MeetRoomController::class, 'store'])->name('meet_rooms.store');
    Route::post('/meet_rooms/{room}/image', [MeetRoomController::class, 'storeImage'])->name('meet_rooms.image');

    // マッチング関連
    Route::get('/matchings/{id}', [MatchingsController::class, 'show'])->name('matchings.show');
    Route::post('/matchings/confirm', [MatchingsController::class, 'confirm'])->name('matchings.confirm');

    // サポーター用依頼一覧
    Route::get('/supports', [SupportController::class, 'index'])->name('supports.index');
    Route::post('/supports/join/{requestId}', [SupportController::class, 'joinRoom'])->name('support.joinRoom');
});


// 管理画面用ルート
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('home');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/categories', [AdminController::class, 'categories'])->name('categories');
    Route::resource('category3', Category3Controller::class)->except(['show']);
    Route::get('/requests', [AdminController::class, 'requests'])->name('requests');
});

require __DIR__.'/auth.php';
