<x-app-layout>
    <x-slot name="header">
        <h2>ユーザー詳細</h2>
    </x-slot>

    <div class="container mx-auto mt-4">
        <!-- 基本情報 -->
        <div class="bg-white p-6 rounded shadow mb-4">
            <h3 class="text-lg font-bold mb-4">基本情報</h3>
            <p><strong>ID:</strong> {{ $user->id }}</p>
            <p><strong>会員区分:</strong> {{ optional($user->membershipClass)->m_name ?? '未設定' }}</p>
            <p><strong>名前:</strong> {{ $user->real_name }}</p>
            <p><strong>名前（カナ）:</strong> {{ $user->real_name_kana }}</p>
            <p><strong>都道府県:</strong> {{ $user->prefecture }}</p>
            <p><strong>住所1:</strong> {{ $user->address1 }}</p>
            <p><strong>住所2:</strong> {{ $user->address2 }}</p>
            <p><strong>電話番号:</strong> {{ $user->tel }}</p>
            <p><strong>性別:</strong>
                @switch($user->gender)
                    @case('male')
                        男性
                        @break
                    @case('female')
                        女性
                        @break
                    @default
                        その他
                @endswitch
            </p>
            <p><strong>生年月日:</strong> {{ $user->birthday }}</p>
            <p><strong>メールアドレス:</strong> {{ $user->email }}</p>
            @if ($user->icon)
                <p><strong>アイコン:</strong></p>
                <img src="{{ asset('storage/' . $user->icon) }}" alt="アイコン" class="w-20 h-20 rounded">
            @endif
        </div>

        <!-- サポーター情報 -->
        @if ($user->membership_id == 3)
        <div class="bg-white p-6 rounded shadow">
            <h3 class="text-lg font-bold mb-4">サポーター情報</h3>
            @if ($supporterProfile->pref_photo)
            <p><strong>プロフィール写真:</strong></p>
            <img src="{{ asset('storage/' . $supporterProfile->pref_photo) }}" alt="プロフィール写真" class="mb-2 max-w-xs rounded">
        @else
            <p class="text-gray-500">プロフィール写真が未登録です。</p>
        @endif

        <p><strong>承認状態:</strong>
            @if ($supporterProfile->ac_id == 1)
                申請中
            @elseif ($supporterProfile->ac_id == 2)
                承認済み
            @elseif ($supporterProfile->ac_id == 5)
                承認解除
            @else
                未登録
            @endif
        </p>

        <!-- 承認・承認解除ボタン -->
        <div class="mt-4">
            @if ($supporterProfile->ac_id == 1)
                <form action="{{ route('admin.users.approve', $supporterProfile->id) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                        承認
                    </button>
                </form>
            @elseif ($supporterProfile->ac_id == 2)
                <form action="{{ route('admin.users.unapprove', $supporterProfile->id) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                        承認解除
                    </button>
                </form>
            @endif
        </div>
            <p><strong>自己紹介:</strong> {{ $supporterProfile->self_introduction ?? '未登録' }}</p>
            @for ($i = 1; $i <= 5; $i++)
                <p><strong>スキル{{ $i }}:</strong> {{ $supporterProfile->{'skill' . $i} ?? '未登録' }}</p>
            @endfor
        </div>
        @endif

        <!-- 操作ボタン -->
        <div class="mt-4">
            <a href="{{ route('admin.users.edit', $user->id) }}" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">編集</a>
            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600" onclick="return confirm('本当に削除しますか？')">削除</button>
            </form>
        </div>
    </div>
</x-app-layout>
