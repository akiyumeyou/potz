<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class GoogleLoginController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            // Googleからユーザー情報を取得
            $socialiteUser = Socialite::driver('google')->user();
            $email = $socialiteUser->email;

            // ユーザーを作成または取得
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $socialiteUser->name,
                    'password' => null, // パスワードは null
                    'email_verified_at' => now(), // メール認証済みとして設定
                ]
            );

            // `email_verified_at` が NULL の場合に更新
            if (is_null($user->email_verified_at)) {
                $user->email_verified_at = now();
                $user->save();
            }

            // ユーザーをログイン
            Auth::login($user);

            // ダッシュボードへリダイレクト
            return redirect()->intended('dashboard');
        } catch (Exception $e) {
            // エラーをログに記録
            Log::error('Google Login Error: ' . $e->getMessage());

            // ログインページにリダイレクト
            return redirect()->route('login')->with('error', 'Googleログインに失敗しました。再試行してください。');
        }
    }
}
