<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\MeetRoomController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\Category3Controller;



Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

use App\Http\Controllers\GoogleLoginController;

Route::get('/auth/google', [GoogleLoginController::class, 'redirectToGoogle'])
    ->name('login.google');

Route::get('/auth/google/callback', [GoogleLoginController::class, 'handleGoogleCallback'])
    ->name('login.google.callback');


Route::middleware('auth')->group(function () {
    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
    Route::patch('/events/{event}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
});


Route::middleware(['auth'])->group(function () {
    Route::get('/requests/create', [RequestController::class, 'create'])->name('requests.create');
    Route::get('/requests/index', [RequestController::class, 'index'])->name('requests.index');
    Route::get('/requests', [RequestController::class, 'index'])->name('index');

    Route::post('/requests', [RequestController::class, 'store'])->name('requests.store');


    Route::get('/meet_rooms/{id}', [MeetRoomController::class, 'show'])->name('meet_rooms.show');
    Route::post('/meet_rooms/{id}', [MeetRoomController::class, 'store'])->name('meet_rooms.store');

    Route::get('/meet_rooms/{id}', [MeetRoomController::class, 'show'])->name('meet_rooms.show');


});


// 管理画面用ルート
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('home');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/categories', [AdminController::class, 'categories'])->name('categories');
    Route::resource('category3', Category3Controller::class)->except(['show']);

    Route::get('/requests', [AdminController::class, 'requests'])->name('requests');
});

