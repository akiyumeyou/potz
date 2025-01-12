<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // ログイン中のユーザーの未読通知を取得
        $notifications = auth()->user()->unreadNotifications;

        // ダッシュボードビューに通知を渡す
        return view('dashboard', compact('notifications'));
    }
}
