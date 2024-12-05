<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('プロフィール情報') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("アカウント情報を更新できます。") }}
        </p>
    </header>
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
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

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('メールアドレスは未確認です。') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900">
                            {{ __('確認メールを再送信') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('新しい確認リンクがメールアドレスに送信されました。') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <!-- 会員区分 -->
        <div>
            <x-input-label for="membership_id" :value="__('会員区分')" />
            <div class="flex items-center gap-4">
                @foreach ($membership_classes as $class)
                    <label class="inline-flex items-center">
                        <input type="radio" name="membership_id" value="{{ $class->id }}"
                               @if ($user->membership_id == $class->id) checked @endif>
                        <span class="ml-2">{{ $class->m_name }}</span>
                    </label>
                @endforeach
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('membership_id')" />
        </div>


<!-- 姓名 -->
<div class="mt-4">
    <label for="real_name" class="block text-sm font-medium text-gray-700">姓名</label>
    <input type="text" name="real_name" id="	real_name" value="{{ old('real_name', $user->real_name) }}" class="form-input mt-1 block w-full">
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
    <label for="address1" class="block text-sm font-medium text-gray-700">住所１</label>
    <input type="text" name="address1" id="address" value="{{ old('address1', $user->address1) }}" class="form-input mt-1 block w-full">
</div>

<!-- 住所2 -->
<div class="mt-4">
    <label for="address2" class="block text-sm font-medium text-gray-700">住所２</label>
    <input type="text" name="address2" id="address" value="{{ old('address2', $user->address2) }}" class="form-input mt-1 block w-full">
</div>

<!-- 電話番号 -->
<div class="mt-4">
    <label for="tel" class="block text-sm font-medium text-gray-700">電話番号</label>
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
        <!-- サポート会員向け案内 -->
    @if ($user->membership_id >= 2)
        <div class="mt-6 p-4 bg-yellow-50 border border-yellow-300 rounded">
            <p class="text-yellow-800 font-semibold">
                {{ __('サポート活動するには認証が必要です、こちらから登録申請してください') }}
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
    @endif

    <!-- 成功メッセージ -->
    @if (session('status') === 'profile-updated')
        <p
            x-data="{ show: true }"
            x-show="show"
            x-transition
            x-init="setTimeout(() => show = false, 2000)"
            class="mt-4 text-sm text-green-600"
        >
            {{ __('内容を保存しました。') }}
        </p>
    @endif
</section>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const membershipInputs = document.querySelectorAll('input[name="membership_id"]');
        const fieldsToValidate = ['real_name', 'address1', 'address2', 'tel', 'birthday'];

        // 会員区分の変更イベントを監視
        membershipInputs.forEach(input => {
            input.addEventListener('change', function () {
                updateRequiredFields(this.value);
            });
        });

        // 初期状態で更新
        const selectedMembership = document.querySelector('input[name="membership_id"]:checked');
        if (selectedMembership) {
            updateRequiredFields(selectedMembership.value);
        }

        function updateRequiredFields(membershipId) {
            fieldsToValidate.forEach(field => {
                const input = document.querySelector(`[name="${field}"]`);
                const label = document.querySelector(`label[for="${field}"]`);

                if (membershipId >= 2) {
                    input.setAttribute('required', 'required');
                    if (label) label.innerHTML = `${label.innerHTML} <span class="text-red-500">*</span>`;
                } else {
                    input.removeAttribute('required');
                    if (label) label.innerHTML = label.innerHTML.replace(/<span class="text-red-500">\*<\/span>/, '');
                }
            });
        }
    });
</script>
