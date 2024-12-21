<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('サポート依頼登録') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-lg mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('requests.store') }}" method="POST" id="supportRequestForm">
                        @csrf

                        <input type="hidden" name="original_request_id" value="{{ $originalRequest->id ?? '' }}">

                        <!-- 依頼カテゴリ -->
                        <div class="mb-4">
                            <label for="category3_id" class="block text-lg font-bold text-gray-700 mb-2">依頼カテゴリ</label>
                            <select name="category3_id" id="category3_id" class="form-control text-lg w-full" required>
                                <option value="">選択してください</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" data-cost="{{ $category->cost }}"
                                        {{ old('category3_id', $originalRequest->category3_id ?? '') == $category->id ? 'selected' : '' }}>
                                        {{ $category->category3 }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 依頼内容 -->
                        <div class="mb-4">
                            <label for="contents" class="block text-lg font-bold text-gray-700 mb-2">具体的な内容</label>
                            <textarea name="contents" id="contents" class="form-control text-lg w-full" rows="4" required>{{ old('contents', $originalRequest->contents ?? '') }}</textarea>
                        </div>

                        <!-- 希望日時 -->
                        <div class="mb-4">
                            <label for="date" class="block text-lg font-bold text-gray-700 mb-2">希望日時</label>
                            <div class="flex space-x-2">
                                <input type="date" name="date" id="date" class="form-control text-lg w-1/2"
                                    value="{{ old('date', $originalRequest->date ?? (isset($originalRequest) ? now()->addDays(1)->format('Y-m-d') : '')) }}">
                                <select name="time_start" id="time_start" class="form-control text-lg w-1/2" required>
                                    <option value="">時刻を選択</option>
                                    @for ($hour = 8; $hour <= 20; $hour++)
                                        <option value="{{ sprintf('%02d:00', $hour) }}"
                                            {{ old('time_start', $originalRequest->time_start ?? '') == sprintf('%02d:00', $hour) ? 'selected' : '' }}>
                                            {{ sprintf('%02d:00', $hour) }}
                                        </option>
                                        <option value="{{ sprintf('%02d:30', $hour) }}"
                                            {{ old('time_start', $originalRequest->time_start ?? '') == sprintf('%02d:30', $hour) ? 'selected' : '' }}>
                                            {{ sprintf('%02d:30', $hour) }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <!-- 作業時間 -->
                        <div class="mb-4">
                            <label for="time" class="block text-lg font-bold text-gray-700 mb-2">サポート依頼時間数</label>
                            <select name="time" id="time" class="form-control text-lg w-full" required>
                                <option value="">時間を選択</option>
                                @for ($i = 0.5; $i <= 8.0; $i += 0.5)
                                    <option value="{{ $i }}" {{ old('time', $originalRequest->time ?? '') == $i ? 'selected' : '' }}>
                                        {{ $i }} 時間
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <!-- 場所 -->
                        <div class="mb-4">
                            <label for="spot" class="block text-lg font-bold text-gray-700 mb-2">場所</label>
                            <select name="spot" id="spot" class="form-control text-lg w-full" required>
                                <option value="自宅" {{ old('spot', $originalRequest->spot ?? '') == '自宅' ? 'selected' : '' }}>自宅</option>
                                <option value="その他" {{ old('spot', $originalRequest->spot ?? '') == 'その他' ? 'selected' : '' }}>その他</option>
                            </select>
                        </div>

                        <!-- その他の住所 -->
                        <div class="mb-4 {{ old('spot', $originalRequest->spot ?? '') == 'その他' ? '' : 'hidden' }}" id="address_field">
                            <label for="address" class="block text-lg font-bold text-gray-700 mb-2">他の住所</label>
                            <input type="text" name="address" id="address" class="form-control text-lg w-full"
                                value="{{ old('address', $originalRequest->address ?? '') }}">
                        </div>

                        <!-- 駐車場 -->
                        <div class="mb-4">
                            <label class="block text-lg font-bold text-gray-700 mb-2">駐車場</label>
                            <div class="flex items-center space-x-4">
                                <label>
                                    <input type="radio" name="parking" value="1" {{ old('parking', $originalRequest->parking ?? '') == 1 ? 'checked' : '' }}>
                                    なし
                                </label>
                                <label>
                                    <input type="radio" name="parking" value="2" {{ old('parking', $originalRequest->parking ?? '') == 2 ? 'checked' : '' }}>
                                    あり
                                </label>
                            </div>
                        </div>

                        <!-- 見積もり金額 -->
                        <div class="mb-4">
                            <label for="estimate" class="block text-lg font-bold text-gray-700 mb-2">見積もり金額</label>
                            <input type="text" id="estimate" name="estimate" class="form-control text-lg w-full"
                                value="{{ isset($originalRequest) ? number_format($originalRequest->estimate, 0) . ' 円' : '' }}" readonly>
                        </div>

                        <!-- 利用規約 -->
                        <div class="mb-4">
                            <label class="inline-flex items-center">
                                <input type="checkbox" id="termsCheck" class="form-checkbox text-blue-500" required>
                                <span class="ml-2 text-gray-700 text-lg">利用規約に同意する</span>
                            </label>
                        </div>

                        <!-- 登録ボタン -->
                        <button
                            type="submit"
                            id="submitButton"
                            class="btn text-lg w-full py-2 bg-gray-400 text-white cursor-not-allowed"
                            disabled>
                            サポート依頼登録
                        </button>

                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


<script>
    // 利用規約に同意しないとボタンを押せない13時
    document.getElementById('termsCheck').addEventListener('change', function (e) {
        document.getElementById('submitButton').disabled = !e.target.checked;
    });

 // 要素を取得
const categorySelect = document.getElementById('category3_id');
const timeInput = document.getElementById('time');
const estimateInput = document.getElementById('estimate');
const spotSelect = document.getElementById('spot');
const addressField = document.getElementById('address_field');

// 見積もり計算
function updateEstimate() {
    const cost = parseFloat(categorySelect.selectedOptions[0]?.getAttribute('data-cost') || 0); // カテゴリのコストを取得
    const time = parseFloat(timeInput.value || 0); // 時間数を取得
    const estimate = (cost * time) + 400; // 計算式: コスト × 時間 + 基本料金
    estimateInput.value = numberWithCommas(estimate) + ' 円'; // 表示をフォーマット
}

// 数字をカンマ区切りにする関数
function numberWithCommas(x) {
    return x.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

// イベントリスナーを設定
categorySelect.addEventListener('change', updateEstimate);
timeInput.addEventListener('change', updateEstimate);

// 場所選択で住所フィールドの表示切り替え
spotSelect.addEventListener('change', () => {
    addressField.classList.toggle('hidden', spotSelect.value !== 'その他');
});

// ページロード時に初期値を再計算
document.addEventListener('DOMContentLoaded', updateEstimate);


    // ボタンの状態を切り替える
    const termsCheck = document.getElementById('termsCheck');
    const submitButton = document.getElementById('submitButton');

    termsCheck.addEventListener('change', function () {
        if (termsCheck.checked) {
            submitButton.classList.remove('bg-gray-400', 'cursor-not-allowed'); // グレーを削除
            submitButton.classList.add('bg-green-500', 'hover:bg-green-600'); // 緑色を追加
            submitButton.disabled = false; // ボタン有効化
        } else {
            submitButton.classList.remove('bg-green-500', 'hover:bg-green-600'); // 緑色を削除
            submitButton.classList.add('bg-gray-400', 'cursor-not-allowed'); // グレーを追加
            submitButton.disabled = true; // ボタン無効化
        }
    });

</script>
