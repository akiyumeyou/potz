<h2 class="text-lg font-medium text-gray-900">会員情報の登録</h2>

<form method="POST" action="{{ route('profile.potzs.member.update') }}">
    @csrf
    @method('PUT') <!-- ここで PUT メソッドを指定 -->

    <!-- 会員区分 -->
    <div class="mt-4">
        <label for="membership_id" class="block text-sm font-medium text-gray-700">会員区分</label>
        <div>
            @foreach ($membership_classes as $class)
                <label class="inline-flex items-center">
                    <input type="radio" name="membership_id" value="{{ $class->id }}"
                        @if ($profile->membership_id == $class->id) checked @endif>
                    <span class="ml-2">{{ $class->m_name }}</span>
                </label>
            @endforeach
        </div>
    </div>

    <!-- 他の入力フィールド -->
    <div class="mt-4">
        <label for="name" class="block text-sm font-medium text-gray-700">本名</label>
        <input type="text" name="name" id="name" value="{{ old('name', $profile->name ?? '') }}" class="form-input mt-1 block w-full">
    </div>

    <div class="mt-4">
        <label for="name_kana" class="block text-sm font-medium text-gray-700">本名（カナ）</label>
        <input type="text" name="name_kana" id="name_kana" value="{{ old('name_kana', $profile->name_kana ?? '') }}" class="form-input mt-1 block w-full">
    </div>

    <div class="mt-4">
        <label for="post" class="block text-sm font-medium text-gray-700">郵便番号</label>
        <input type="text" name="post" id="post" value="{{ old('post', $profile->post ?? '') }}" class="form-input mt-1 block w-full">
    </div>

    <div class="mt-4">
        <label for="address" class="block text-sm font-medium text-gray-700">住所</label>
        <input type="text" name="address" id="address" value="{{ old('address', $profile->address ?? '') }}" class="form-input mt-1 block w-full">
    </div>

    <div class="mt-4">
        <label for="tel" class="block text-sm font-medium text-gray-700">電話番号</label>
        <input type="text" name="tel" id="tel" value="{{ old('tel', $profile->tel ?? '') }}" class="form-input mt-1 block w-full">
    </div>

    <div class="mt-4">
        <label for="birthday" class="block text-sm font-medium text-gray-700">生年月日</label>
        <input type="date" name="birthday" id="birthday" value="{{ old('birthday', $profile->birthday ?? '') }}" class="form-input mt-1 block w-full">
    </div>

    <!-- 保存ボタン -->
    <div class="mt-6">
        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">
            {{ __('保存') }}
        </button>
    </div>
</form>
