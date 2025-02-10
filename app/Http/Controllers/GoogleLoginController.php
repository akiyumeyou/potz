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

            // name が null の場合、"ゲスト" に設定
            $userName = $socialiteUser->name ?? 'ゲスト';

            // ユーザーを作成または取得
            $user = User::firstOrCreate(
                ['email' => $socialiteUser->email],
                [
                    'name' => $userName,
                    'password' => null, // パスワードは null
                    'email_verified_at' => now(), // メール認証済みとして設定
                ]
            );

            // `email_verified_at` が NULL の場合、最初のログイン時に設定
            if (is_null($user->email_verified_at)) {
                $user->email_verified_at = now();
                $user->save(); // 更新を保存
            }

            // ユーザーをログイン
            Auth::login($user, true); // true: "Remember Me"を有効化

            // ダッシュボードへリダイレクト
            return redirect()->intended('dashboard');
        } catch (\Exception $e) {
            // エラーの詳細をログに記録
            Log::error('Google Login Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            // ログインページにリダイレクト
            return redirect()->route('login')->with('error', 'Googleログインに失敗しました。再試行してください。');
        }
    }
}

// class GoogleLoginController extends Controller
// {
//     public function redirectToGoogle()
//     {
//         return Socialite::driver('google')->redirect();
//     }
//     public function handleGoogleCallback()
//     {
//         try {
//             // Googleからユーザー情報を取得
//             $socialiteUser = Socialite::driver('google')->user();

//             // ユーザーを作成または取得
//             $user = User::firstOrCreate(
//                 ['email' => $socialiteUser->email],
//                 [
//                     'name' => $socialiteUser->name,
//                     'password' => null, // パスワードは null
//                     'email_verified_at' => now(), // メール認証済みとして設定
//                 ]
//             );

//             //  // `email_verified_at` が NULL の場合、最初のログイン時に設定
//             if (is_null($user->email_verified_at)) {
//             $user->email_verified_at = now();
//             $user->save(); // 更新を保存
//             }

//             // ユーザーをログイン
//             Auth::login($user, true); // true: "Remember Me"を有効化

//             // ダッシュボードへリダイレクト
//             return redirect()->intended('dashboard');
//         } catch (\Exception $e) {
//             // エラーの詳細をログに記録
//             Log::error('Google Login Error: ' . $e->getMessage(), [
//                 'trace' => $e->getTraceAsString(),
//             ]);

//             // ログインページにリダイレクト
//             return redirect()->route('login')->with('error', 'Googleログインに失敗しました。再試行してください。');
//         }

//     }
// }
