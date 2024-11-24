<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * 管理画面のホーム
     */
    public function index()
    {
        // 管理者チェック
        if (!$this->isAdmin()) {
            abort(403, 'このページへのアクセスは許可されていません。');
        }

        return view('admin.home');
    }

    /**
     * ユーザー一覧
     */
    public function users()
    {
        if (!$this->isAdmin()) {
            abort(403, 'このページへのアクセスは許可されていません。');
        }

        $users = \App\Models\User::all();
        return view('admin.users.index', compact('users'));
    }

    /**
     * カテゴリ一覧
     */
    public function categories()
    {
        if (!$this->isAdmin()) {
            abort(403, 'このページへのアクセスは許可されていません。');
        }

        $categories = \App\Models\Category3::all();
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * サポート一覧
     */
    public function requests()
    {
        if (!$this->isAdmin()) {
            abort(403, 'このページへのアクセスは許可されていません。');
        }

        $requests = \App\Models\UserRequest::all();
        return view('admin.requests.index', compact('requests'));
    }

    /**
     * 管理者チェックロジック
     */
    private function isAdmin()
    {
        // 固定管理者メールアドレス
        $adminEmail = 'admin@potz.admin';

        return Auth::check() && Auth::user()->email === $adminEmail;
    }
}
