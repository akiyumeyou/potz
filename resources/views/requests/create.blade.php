<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('依頼作成') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('requests.store') }}" method="POST">
                        @csrf

                        <!-- カテゴリ選択 -->
                        <div class="mb-4">
                            <label for="category3_id" class="form-label text-lg font-bold">カテゴリ</label>
                            <select name="category3_id" id="category3_id" class="form-control text-lg" required>
                                <option value="">カテゴリを選択</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" data-cost="{{ $category->cost }}">
                                        {{ $category->category3 }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 依頼内容 -->
                        <div class="mb-4">
                            <label for="contents" class="form-label text-lg font-bold">依頼内容</label>
                            <textarea name="contents" id="contents" class="form-control text-lg" rows="5" required></textarea>
                        </div>

                        <!-- 日時選択 -->
                        <div class="mb-4">
                            <label for="date" class="form-label text-lg font-bold">希望日時</label>
                            <div class="d-flex align-items-center">
                                <input type="date" name="date" id="date" class="form-control text-lg me-2" required>
                                <select name="time_start" id="time_start" class="form-control text-lg" required>
                                    <option value="">時刻を選択</option>
                                    @for ($hour = 8; $hour <= 20; $hour++)
                                        <option value="{{ sprintf('%02d:00', $hour) }}">{{ sprintf('%02d:00', $hour) }}</option>
                                        <option value="{{ sprintf('%02d:30', $hour) }}">{{ sprintf('%02d:30', $hour) }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <!-- 作業時間 -->
                        <div class="mb-4">
                            <label for="time" class="form-label text-lg font-bold">作業時間</label>
                            <select name="time" id="time" class="form-control text-lg" required>
                                <option value="">作業時間を選択</option>
                                @for ($i = 0.5; $i <= 8.0; $i += 0.5)
                                    <option value="{{ number_format($i, 1) }}">{{ number_format($i, 1) }} 時間</option>
                                @endfor
                            </select>
                        </div>

                        <!-- 場所 -->
                        <div class="mb-4">
                            <label for="spot" class="form-label text-lg font-bold">場所</label>
                            <select name="spot" id="spot" class="form-control text-lg" required>
                                <option value="自宅">自宅</option>
                                <option value="その他">その他</option>
                            </select>
                        </div>

                        <!-- 住所 -->
                        <div class="mb-4 d-none" id="address_field">
                            <label for="address" class="form-label text-lg font-bold">住所</label>
                            <input type="text" name="address" id="address" class="form-control text-lg">
                        </div>

                        <!-- 駐車場 -->
                        <div class="mb-4">
                            <label class="form-label text-lg font-bold">駐車場</label>
                            <div>
                                <input type="radio" id="parking_1" name="parking" value="1" required>
                                <label for="parking_1">なし</label>
                                <input type="radio" id="parking_2" name="parking" value="2" required>
                                <label for="parking_2">あり</label>
                            </div>
                        </div>

                        <!-- 見積もり金額 -->
                        <div class="mb-4">
                            <label for="estimate" class="form-label text-lg font-bold">見積もり金額</label>
                            <input type="text" id="estimate" name="estimate" class="form-control text-lg" readonly>
                        </div>

                        <!-- 登録ボタン -->
                        <button type="submit" class="btn btn-success text-lg w-full py-2">依頼を登録</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    const categorySelect = document.getElementById('category3_id');
    const timeInput = document.getElementById('time');
    const estimateInput = document.getElementById('estimate');
    const spotSelect = document.getElementById('spot');
    const addressField = document.getElementById('address_field');

    // 場所選択変更時の処理
    spotSelect.addEventListener('change', (e) => {
        addressField.classList.toggle('d-none', e.target.value !== 'その他');
    });

    // 見積もり計算
    function updateEstimate() {
        const cost = parseFloat(categorySelect.selectedOptions[0]?.getAttribute('data-cost') || 0);
        const time = parseFloat(timeInput.value || 0);
        const estimate = (cost * time) + 400; // 基本料金 x 時間 + 交通費
        estimateInput.value = estimate.toFixed(1);
    }

    document.addEventListener('DOMContentLoaded', () => {
        categorySelect.addEventListener('change', updateEstimate);
        timeInput.addEventListener('change', updateEstimate);
    });
</script>
