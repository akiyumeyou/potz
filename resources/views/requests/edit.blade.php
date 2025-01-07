<div class="py-12"></div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="text-xl font-bold mb-4">依頼内容： {{ $userRequest->category3->category3 ?? '未設定' }}</h3>
            </div>

                <!-- 一覧表示 -->
                <table class="min-w-full table-auto border-collapse border border-gray-400 mb-6">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border border-gray-300 px-4 py-2">内容</th>
                            <th class="border border-gray-300 px-4 py-2">日時</th>
                            <th class="border border-gray-300 px-4 py-2">開始時刻</th>
                            <th class="border border-gray-300 px-4 py-2">支援時間</th>
                           <th class="border border-gray-300 px-4 py-2">見込み金額</th>
                            <th class="border border-gray-300 px-4 py-2">訪問先</th>
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
                                        <option value="{{ sprintf('%02d:00', $hour) }}" {{ $userRequest->time_start == sprintf('%02d:00', $hour) ? 'selected' : '' }}>
                                            {{ sprintf('%02d:00', $hour) }}
                                        </option>
                                        <option value="{{ sprintf('%02d:30', $hour) }}" {{ $userRequest->time_start == sprintf('%02d:30', $hour) ? 'selected' : '' }}>
                                            {{ sprintf('%02d:30', $hour) }}
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
                            <!-- 見込み金額の再表示 -->
<td class="border border-gray-300 px-4 py-2 text-right">
        {{ ceil($userRequest->estimate) }}円

    </td>
</td>

                            <td class="border border-gray-300 px-4 py-2">

                                <button class="bg-gray-500 text-white px-2 py-1 rounded hover:bg-gray-700" onclick="showDetails()">
                                    詳細確認
                                </button>
                            </td>
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

                    <button type="submit" class="px-6 py-1 bg-green-500 text-white text-xl font-bold rounded shadow hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-300">
                        保存する
                    </button>
                </form>
<!-- モーダル -->
<div id="details-modal" class="hidden fixed z-50 inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center">
    <div class="bg-white p-4 rounded-lg shadow-lg">
        <h2 class="text-xl font-bold mb-2">訪問先の詳細</h2>
        <p>依頼者: {{ $userRequest->user->real_name }}</p>
        <p>住所: {{ $userRequest->user->prefecture }} {{ $userRequest->user->address1 }} {{ $userRequest->user->address2 }}</p>
        <p>電話番号: {{ $userRequest->user->tel }}</p>
        <button class="bg-red-500 text-white px-4 py-2 rounded mt-4" onclick="closeModal()">閉じる</button>
    </div>
</div>
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
        const timeStart = document.querySelector('[name="time_start"]').value;
        console.log('送信される time_start:', timeStart); // 送信前に値を確認
        document.getElementById('form-contents').value = document.querySelector('[name="contents"]').value;
        document.getElementById('form-date').value = document.querySelector('[name="date"]').value;
        document.getElementById('form-time_start').value = document.querySelector('[name="time_start"]').value;
        document.getElementById('form-time').value = document.querySelector('[name="time"]').value;

    });

    function showDetails() {
        document.getElementById('details-modal').classList.remove('hidden');
    }
    function closeModal() {
        document.getElementById('details-modal').classList.add('hidden');
    }
</script>
