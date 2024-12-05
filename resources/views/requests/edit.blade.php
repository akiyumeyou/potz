<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="text-xl font-bold mb-4">現在の依頼内容：</h3>

                <!-- カテゴリ表示 -->
                <div class="mb-4">
                    <label class="font-bold">カテゴリ:</label>
                    <p class="text-gray-700">{{ $userRequest->category->category3 ?? '未設定' }}</p>
                </div>

                <!-- 一覧表示 -->
                <table class="min-w-full table-auto border-collapse border border-gray-400 mb-6">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border border-gray-300 px-4 py-2">内容</th>
                            <th class="border border-gray-300 px-4 py-2">日時</th>
                            <th class="border border-gray-300 px-4 py-2">開始時刻</th>
                            <th class="border border-gray-300 px-4 py-2">支援時間</th>
                            <th class="border border-gray-300 px-4 py-2">場所</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border border-gray-300 px-4 py-2">
                                <span class="editable-field" data-name="contents">{{ $userRequest->contents }}</span>
                                <textarea class="hidden editable-input w-full form-control" name="contents">{{ $userRequest->contents }}</textarea>
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                <span class="editable-field" data-name="date">
                                    {{ \Carbon\Carbon::parse($userRequest->date)->isoFormat('YYYY年MM月DD日（ddd）') }}
                                </span>
                                <input type="date" class="hidden editable-input w-full form-control" name="date" value="{{ \Carbon\Carbon::parse($userRequest->date)->format('Y-m-d') }}">
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                <span class="editable-field" data-name="time_start">{{ \Carbon\Carbon::parse($userRequest->time_start)->format('H:i') }}</span>
                                <select class="hidden editable-input w-full form-control" name="time_start">
                                    <option value="">時刻を選択</option>
                                    @for ($hour = 8; $hour <= 20; $hour++)
                                        <option value="{{ $hour }}:00" {{ $userRequest->time_start == "$hour:00" ? 'selected' : '' }}>
                                            {{ $hour }}:00
                                        </option>
                                        <option value="{{ $hour }}:30" {{ $userRequest->time_start == "$hour:30" ? 'selected' : '' }}>
                                            {{ $hour }}:30
                                        </option>
                                    @endfor
                                </select>
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                <span class="editable-field" data-name="time">{{ number_format($userRequest->time, 1) }} 時間</span>
                                <select class="hidden editable-input w-full form-control" name="time">
                                    <option value="">時間を選択</option>
                                    @for ($i = 0.5; $i <= 8.0; $i += 0.5)
                                        <option value="{{ $i }}" {{ $userRequest->time == $i ? 'selected' : '' }}>
                                            {{ $i }} 時間
                                        </option>
                                    @endfor
                                </select>
                            </td>
                            <td class="border border-gray-300 px-4 py-2">{{ $userRequest->spot }}</td>
                        </tr>
                    </tbody>
                </table>

                <!-- 保存ボタン -->
                <form id="edit-form" action="{{ route('requests.update', $userRequest->id) }}" method="POST" class="hidden mt-6">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="contents" id="form-contents" value="{{ $userRequest->contents }}">
                    <input type="hidden" name="date" id="form-date" value="{{ $userRequest->date }}">
                    <input type="hidden" name="time_start" id="form-time_start" value="{{ $userRequest->time_start }}">
                    <input type="hidden" name="time" id="form-time" value="{{ $userRequest->time }}">

                    <button type="submit" class="px-6 py-3 bg-green-500 text-white text-xl font-bold rounded shadow hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-300">
                        保存する
                    </button>
                </form>

            </div>
        </div>
    </div>
</div>

<script>
    // 編集モードの切り替え
    document.querySelectorAll('.editable-field').forEach(function (field) {
        field.addEventListener('click', function () {
            const name = this.dataset.name;
            this.classList.add('hidden');
            document.querySelector(`[name="${name}"]`).classList.remove('hidden');
            document.querySelector('#edit-form').classList.remove('hidden'); // 保存ボタン表示
        });
    });

    // フォーム送信時に値を設定
    document.querySelector('#edit-form').addEventListener('submit', function (event) {
        document.getElementById('form-contents').value = document.querySelector('[name="contents"]').value;
        document.getElementById('form-date').value = document.querySelector('[name="date"]').value;
        // document.getElementById('form-time_start').value = document.querySelector('[name="time_start"]').value;
        // document.getElementById('form-time').value = document.querySelector('[name="time"]').value;
    });
</script>
