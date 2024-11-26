<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\UserProfile;
use App\Models\MembershipClass;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        // ユーザー情報とプロファイル情報を取得
        $user = $request->user();
        $profile = UserProfile::where('user_id', $user->id)->first();

        // プロファイルが存在しない場合、新規作成
        if (!$profile) {
            $profile = UserProfile::create(['user_id' => $user->id]);
        }

        // 会員区分の取得
        $membership_classes = MembershipClass::all();

        // ビューにデータを渡す
        return view('profile.edit', [
            'user' => $user,
            'profile' => $profile,
            'membership_classes' => $membership_classes,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'membership_id' => 'required|exists:membership_classes,id',
            'name' => 'required|string|max:255',
            'name_kana' => 'required|string|max:255',
            'post' => 'required|string|max:8',
            'address' => 'required|string|max:255',
            'tel' => 'required|string|max:15',
            'birthday' => 'required|date',
        ]);

        $profile = UserProfile::where('user_id', $request->user()->id)->first();
        if ($profile) {
            $profile->update([
                'membership_id' => $request->input('membership_id'),
                'name' => $request->input('name'),
                'name_kana' => $request->input('name_kana'),
                'post' => $request->input('post'),
                'address' => $request->input('address'),
                'tel' => $request->input('tel'),
                'birthday' => $request->input('birthday'),
            ]);
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
