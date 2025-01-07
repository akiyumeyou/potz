<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('プロフィール情報') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("アカウント情報を更新できます。") }}
                <!-- 完了メッセージ -->
    @if (session('status') === 'profile-updated')
    <div class="mt-6 bg-green-50 border border-green-300 text-green-700 p-4 rounded">
        <p>プロフィールが正常に更新されました。</p>
        <a href="{{ route('dashboard') }}" class="mt-2 inline-block bg-blue-500 text-white font-semibold py-2 px-4 rounded hover:bg-blue-600">
            ダッシュボードに戻る
        </a>
    </div>
@endif
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form id="profileForm" method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">

        @csrf
        @method('patch')

        <!-- 表示名 -->
        <div>
            <x-input-label for="name" :value="__('表示名')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                          :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <!-- メールアドレス -->
        <div>
            <x-input-label for="email" :value="__('メールアドレス')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                          :value="old('email', $user->email)" required autocomplete="email" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <!-- 会員区分 -->
        <div>
            <x-input-label for="membership_id" :value="__('会員区分')" />
            <div class="flex items-center gap-4">
                <label class="inline-flex items-center">
                    <input type="radio" name="membership_id" value="1"
                           {{ old('membership_id', $user->membership_id) == 1 ? 'checked' : '' }}>
                    <span class="ml-2">一般会員</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="radio" name="membership_id" value="2"
                           {{ old('membership_id', $user->membership_id) == 2 ? 'checked' : '' }}>
                    <span class="ml-2">POTZ会員</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="radio" name="membership_id" value="3"
                           {{ old('membership_id', $user->membership_id) == 3 ? 'checked' : '' }}>
                    <span class="ml-2">サポート会員</span>
                </label>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('membership_id')" />
        </div>

        <!-- 姓名 -->
        <div class="mt-4">
            <label for="real_name" class="block text-sm font-medium text-gray-700">姓名 <span class="text-red-500">*</span></label>
            <input type="text" name="real_name" id="real_name" value="{{ old('real_name', $user->real_name) }}" class="form-input mt-1 block w-full">
        </div>

        <!-- カナ -->
        <div class="mt-4">
            <label for="real_name_kana" class="block text-sm font-medium text-gray-700">ナマエ（フリガナ）</label>
            <input type="text" name="real_name_kana" id="real_name_kana" value="{{ old('real_name_kana', $user->real_name_kana) }}" class="form-input mt-1 block w-full">
        </div>
        <!-- 性別 -->
        <div>
            <x-input-label for="gender" :value="__('性別')" />
            <div class="flex items-center gap-4">
                <label>
                    <input type="radio" name="gender" value="male"
                        {{ $user->gender == 'male' ? 'checked' : '' }}>
                    <span class="ml-2">{{ __('男性') }}</span>
                </label>
                <label>
                    <input type="radio" name="gender" value="female"
                        {{ $user->gender == 'female' ? 'checked' : '' }}>
                    <span class="ml-2">{{ __('女性') }}</span>
                </label>
                <label>
                    <input type="radio" name="gender" value="other"
                        {{ $user->gender == 'other' ? 'checked' : '' }}>
                    <span class="ml-2">{{ __('その他') }}</span>
                </label>
            </div>
        <!-- 都道府県 -->
        <div class="mt-4">
            <label for="prefecture" class="block text-sm font-medium text-gray-700">都道府県</label>
            <input type="text" name="prefecture" id="prefecture" value="{{ old('prefecture', $user->prefecture) }}" class="form-input mt-1 block w-full">
        </div>

        <!-- 住所1 -->
        <div class="mt-4">
            <label for="address1" class="block text-sm font-medium text-gray-700">住所１<span class="text-red-500">*</span></label>
            <input type="text" name="address1" id="address" value="{{ old('address1', $user->address1) }}" class="form-input mt-1 block w-full">
        </div>

        <!-- 住所2 -->
        <div class="mt-4">
            <label for="address2" class="block text-sm font-medium text-gray-700">住所２</label>
            <input type="text" name="address2" id="address" value="{{ old('address2', $user->address2) }}" class="form-input mt-1 block w-full">
        </div>

        <!-- 電話番号 -->
        <div class="mt-4">
            <label for="tel" class="block text-sm font-medium text-gray-700">電話番号 <span class="text-red-500">*</span></label>
            <input type="text" name="tel" id="tel" value="{{ old('tel', $user->tel) }}" class="form-input mt-1 block w-full">
        </div>
        <!-- 生年月日 -->
        <div>
            <x-input-label for="birthday" :value="__('生年月日')" />
            <x-text-input id="birthday" name="birthday" type="date" class="mt-1 block w-full"
                          :value="old('birthday', $user->birthday)" />
            <x-input-error class="mt-2" :messages="$errors->get('birthday')" />
        </div>

        <div class="flex items-center gap-4"></div>
            <x-primary-button>{{ __('保存') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>

    </form>
   <!-- サポートプロフィール案内 -->
    <!-- サポートプロフィールエリア -->
    <div id="supporter-section" class="mt-6 p-4 bg-yellow-50 border border-yellow-300 rounded hidden">
        <p class="text-yellow-800 font-semibold">
            {{ __('サポート活動するには認証が必要です。こちらから登録申請してください。') }}
        </p>
        <a href="{{ route('supporter-profile.edit') }}"
           class="mt-2 inline-block bg-yellow-500 text-white font-semibold py-2 px-4 rounded hover:bg-yellow-600">
            {{ __('プロフィール設定を申請') }}
        </a>
          @if ($user->supporterProfile)
             <div class="mt-4">
                 <label class="block text-sm font-medium text-gray-700">申請状況:</label>
                 <p>
                    @if ($user->supporterProfile)
            <script>
                console.log('ac_id:', {{ $user->supporterProfile->ac_id }});
            </script>
            @else
            <script>
                console.log('サポートプロフィールが存在しません');
            </script>
            @endif
            @if ($user->supporterProfile->ac_id === 1)
                <span class="text-yellow-500">申請中</span>
            @elseif ($user->supporterProfile->ac_id === 2)
                <span class="text-green-500">承認済み</span>
            @else
                <span class="text-gray-500">未申請</span>
            @endif
            </p>
            </div>
    @endif
</div>


</sction>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('profileForm');
    const membershipRadios = document.querySelectorAll('input[name="membership_id"]');
    const supporterSection = document.getElementById('supporter-section');
    const fieldsToValidate = ['name', 'email', 'real_name', 'tel', 'address1'];

    // サポートプロフィールエリアの表示切り替え
    membershipRadios.forEach(radio => {
        radio.addEventListener('change', function () {
            const selectedValue = document.querySelector('input[name="membership_id"]:checked').value;
            if (selectedValue === '3') {
                supporterSection.classList.remove('hidden');
            } else {
                supporterSection.classList.add('hidden');
            }
        });
    });

    // フォームの必須項目バリデーション
    form.addEventListener('submit', function (e) {
        let isValid = true;

        fieldsToValidate.forEach(field => {
            const input = document.getElementById(field);
            if (!input || input.value.trim() === '') {
                isValid = false;
                input.classList.add('border-red-500');
                input.focus(); // 最初の未入力項目にフォーカス
            } else {
                input.classList.remove('border-red-500');
            }
        });

        if (!isValid) {
            e.preventDefault(); // フォーム送信を停止
        }
    });

    // 初期表示の状態更新
    const initialMembership = document.querySelector('input[name="membership_id"]:checked');
    if (initialMembership && initialMembership.value === '3') {
        supporterSection.classList.remove('hidden');
    }
});


</script>
