<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('サポートを頼む') }}
            </h2>
            <a href="{{ route('requests.index') }}"
               class="px-4 py-2 bg-blue-900 text-white text-sm font-bold rounded shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-gray-300">
                戻る
            </a>
        </div>
    </x-slot>

    @if (!request('from_request'))
    <!-- 履歴から依頼ボタンを中央に配置 -->
    <div class="flex justify-center my-6">
        <button id="history-button" class="bg-blue-500 text-white px-6 py-3 rounded hover:bg-blue-700 text-lg">
            履歴から頼む
        </button>
    </div>

    <!-- モーダルの構造 -->
    <div id="history-modal" class="hidden fixed inset-0 z-50 bg-gray-900 bg-opacity-50 flex justify-center items-center">
        <div class="bg-white rounded-lg shadow-lg w-11/12 max-w-lg p-6 relative">
            <h2 class="text-lg font-bold mb-4">過去の履歴一覧</h2>
            <div class="space-y-4">
                @forelse ($requestHistories ?? [] as $history)
                    <div class="border rounded p-4 shadow history-item">
                        <p class="font-semibold text-gray-800">
                            {{ \Carbon\Carbon::parse($history->created_at)->isoFormat('YYYY年MM月DD日（dddd）') }}
                        </p>
                        <p class="text-gray-600">{{ $history->category3->category3 ?? '未設定' }}</p>
                        <p class="text-gray-500">{{ $history->contents }}</p>
                        <button class="bg-blue-500 text-white px-4 py-2 rounded mt-2"
                                onclick="useHistory({{ json_encode($history) }})">
                            この履歴を使用
                        </button>
                    </div>
                @empty
                    <p class="text-gray-500">履歴がありません。</p>
                @endforelse
            </div>
            <button class="absolute top-2 right-2 text-gray-500 hover:text-gray-700" onclick="closeModal()">×</button>
        </div>
    </div>
@endif


    <div class="py-1">

        <div class="max-w-lg mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('requests.store') }}" method="POST" id="supportRequestForm">
                        @csrf

                        <input type="hidden" name="original_request_id" value="{{ $originalRequest->id ?? '' }}">
                        <input type="hidden" id="distance" value="{{ $originalRequest->distance ?? 0 }}">
                        <input type="hidden" id="transport_rate" value="15">

                        <!-- 依頼カテゴリ -->
                        <div class="mb-4">
                            <label for="category3_id" class="block text-lg font-bold text-gray-700 mb-2">カテゴリ</label>
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
                            <label for="time" class="block text-lg font-bold text-gray-700 mb-2">頼む時間数</label>
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
                            <label for="estimate" class="block text-lg font-bold text-gray-700 mb-2">めやす金額（交通費仮４００円加算済）</label>
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
                            サポートを登録
                        </button>

                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    // 必要な要素を取得
    const spotSelect = document.getElementById('spot'); // 場所選択
    const addressField = document.getElementById('address_field'); // その他住所入力フィールド
    const categorySelect = document.getElementById('category3_id'); // カテゴリ選択
    const timeInput = document.getElementById('time'); // 作業時間入力
    const timeStartInput = document.getElementById('time_start'); // 開始時間
    const estimateInput = document.getElementById('estimate'); // 見積もり金額
    const distanceInput = document.getElementById('distance'); // 距離
    const transportRateInput = document.getElementById('transport_rate'); // 輸送単価
    const termsCheck = document.getElementById('termsCheck'); // 利用規約チェック
    const submitButton = document.getElementById('submitButton'); // 登録ボタン

    // **1. 場所選択による住所フィールドの表示切り替え**
    if (spotSelect && addressField) {
        // ページロード時の初期設定
        addressField.classList.toggle('hidden', spotSelect.value !== 'その他');

        // 場所変更時の動作
        spotSelect.addEventListener('change', () => {
            const isOther = spotSelect.value === 'その他';
            addressField.classList.toggle('hidden', !isOther);

            // その他が選ばれた場合、アドレスをセット
            if (!isOther) {
                document.getElementById('address').value = ''; // 入力内容をクリア
            }
        });
    }

    // **2. 見積もり金額の計算ロジック**
    function updateEstimate() {
        const cost = parseFloat(categorySelect.selectedOptions[0]?.getAttribute('data-cost') || 0);
        const time = parseFloat(timeInput.value || 0);
        const distance = parseFloat(distanceInput.value || 0);
        const transportRate = parseFloat(transportRateInput.value || 15);

        // 輸送費の計算
        const transportCost = distance > 0 ? distance * transportRate * 2 : 400;

        // 見積もり金額を計算
        const estimate = (cost * time) + transportCost;

        // 見積もり金額をセット（カンマ区切り）
        estimateInput.value = `${numberWithCommas(estimate)} 円`;
    }

    // 数値をカンマ区切りでフォーマット
    function numberWithCommas(x) {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    // **3. 履歴モーダル**
    function openModal() {
        document.getElementById('history-modal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('history-modal').classList.add('hidden');
    }

    function useHistory(history) {
        // 履歴からフォームに値をセット
        document.getElementById('category3_id').value = history.category3_id;
        document.getElementById('contents').value = history.contents;
        document.getElementById('spot').value = history.spot;
        document.getElementById('time_start').value = history.time_start; // 開始時間をセット
        document.getElementById('time').value = history.time; // 作業時間をセット
        document.querySelector(`input[name="parking"][value="${history.parking}"]`).checked = true; // 駐車場
        document.getElementById('date').value = getTomorrowDate(); // 明日の日付をセット
        updateEstimate(); // 見積もり金額を再計算
        closeModal(); // モーダルを閉じる
    }

    // **4. 明日の日付を取得**
    function getTomorrowDate() {
        const today = new Date();
        today.setDate(today.getDate() + 1);
        return today.toISOString().split('T')[0];
    }

    // **5. ページロード時の初期処理**
    document.addEventListener('DOMContentLoaded', () => {
        // 見積もり計算の初期化
        updateEstimate();

        // 履歴ボタンイベント
        document.getElementById('history-button').addEventListener('click', openModal);
    });

    // **6. 利用規約のチェック状態に応じて登録ボタンの有効化**
    termsCheck.addEventListener('change', function () {
        const isChecked = termsCheck.checked;
        submitButton.disabled = !isChecked;
        submitButton.classList.toggle('bg-gray-400', !isChecked);
        submitButton.classList.toggle('bg-green-500', isChecked);
        // カーソルスタイルの切り替え
        submitButton.classList.toggle('cursor-not-allowed', !isChecked);
        submitButton.classList.toggle('cursor-pointer', isChecked);
    });

    // 見積もり更新のイベント設定
    categorySelect.addEventListener('change', updateEstimate);
    timeInput.addEventListener('input', updateEstimate);

</script>

