<x-app-layout>
    <x-slot name="header">
        <h2>ユーザー詳細</h2>
    </x-slot>

    <div class="container mx-auto mt-4">
        <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
            @csrf
            @method('PUT')

            <!-- ユーザー情報 -->
            <div class="bg-white p-6 rounded shadow mb-4">
                <h3 class="text-lg font-bold mb-4">基本情報</h3>
                <div class="mb-4">
                    <label for="real_name" class="block text-gray-700">氏名</label>
                    <input type="text" name="real_name" id="real_name" value="{{ $user->real_name }}" class="form-input w-full">
                </div>
                <div class="mb-4">
                    <label for="real_name_kana" class="block text-gray-700">氏名（カナ）</label>
                    <input type="text" name="real_name_kana" id="real_name_kana" value="{{ $user->real_name_kana }}" class="form-input w-full">
                </div>
                <div class="mb-4">
                    <label for="prefecture" class="block text-gray-700">都道府県</label>
                    <input type="text" name="prefecture" id="prefecture" value="{{ $user->prefecture }}" class="form-input w-full">
                </div>
                <div class="mb-4">
                    <label for="address1" class="block text-gray-700">住所1</label>
                    <input type="text" name="address1" id="address1" value="{{ $user->address1 }}" class="form-input w-full">
                </div>
                <div class="mb-4">
                    <label for="address2" class="block text-gray-700">住所2</label>
                    <input type="text" name="address2" id="address2" value="{{ $user->address2 }}" class="form-input w-full">
                </div>
                <div class="mb-4">
                    <label for="tel" class="block text-gray-700">電話番号</label>
                    <input type="text" name="tel" id="tel" value="{{ $user->tel }}" class="form-input w-full">
                </div>
                <div class="mb-4">
                    <label for="gender" class="block text-gray-700">性別</label>
                    <select name="gender" id="gender" class="form-select w-full">
                        <option value="male" {{ $user->gender == 'male' ? 'selected' : '' }}>男性</option>
                        <option value="female" {{ $user->gender == 'female' ? 'selected' : '' }}>女性</option>
                        <option value="other" {{ $user->gender == 'other' ? 'selected' : '' }}>その他</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="birthday" class="block text-gray-700">生年月日</label>
                    <input type="date" name="birthday" id="birthday" value="{{ $user->birthday }}" class="form-input w-full">
                </div>
            </div>

            <!-- サポーター情報 -->
            @if ($user->membership_id == 3) <!-- サポーター会員区分 -->
            <div class="bg-white p-6 rounded shadow mb-4">
                <h3 class="text-lg font-bold mb-4">サポーター情報</h3>
                <div class="mb-4">
                    <label for="pref_photo" class="block text-gray-700">プロフィール写真</label>
                    @if ($supporterProfile->pref_photo)
                        <img src="{{ asset('storage/' . $supporterProfile->pref_photo) }}" alt="プロフィール写真" class="mb-2 max-w-xs rounded">
                    @else
                        <p class="text-gray-500">未登録</p>
                    @endif
                    @if ($supporterProfile->ac_id != 2)
                        <button type="button" onclick="approvePhoto({{ $supporterProfile->id }})" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">承認</button>
                    @endif
                </div>
                <div class="mb-4">
                    <label for="ac_id" class="block text-gray-700">承認状態</label>
                    <p>
                        @if ($supporterProfile->ac_id == 1)
                            申請中
                        @elseif ($supporterProfile->ac_id == 2)
                            承認済み
                        @else
                            未登録
                        @endif
                    </p>
                </div>
                <div class="mb-4">
                    <label for="self_introduction" class="block text-gray-700">自己紹介</label>
                    <textarea name="self_introduction" id="self_introduction" class="form-input w-full">{{ $supporterProfile->self_introduction }}</textarea>
                </div>
               
            @endif

            <div class="flex space-x-4">
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">更新</button>
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">キャンセル</a>
            </div>
        </form>
    </div>

    <script>
        function approvePhoto(profileId) {
            if (confirm('プロフィール写真を承認しますか？')) {
                fetch(`/admin/supporter_profiles/${profileId}/approve`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    }
                }).then(response => {
                    if (response.ok) {
                        alert('承認しました');
                        location.reload();
                    } else {
                        alert('承認に失敗しました');
                    }
                });
            }
        }
    </script>
</x-app-layout>
