<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('サポート - 依頼一覧') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-bold mb-4">依頼一覧</h3>

                    <!-- フィルタ機能 -->
                    <div class="flex space-x-4 mb-6">
                        <button class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-700" onclick="filterRequests('all')">すべて</button>
                        <button class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-700" onclick="filterRequests('own')">自分の案件</button>
                        <button class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-700" onclick="filterRequests('new')">新規案件</button>
                        <button class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-700" onclick="filterRequests('completed')">終了済み</button>
                    </div>

                    <!-- 一覧表示 -->
                    <div id="request-list" class="hidden sm:block">
                        @if ($requests->isNotEmpty())
                            <table class="table-auto w-full border-collapse border border-gray-300">
                                <thead>
                                    <tr>
                                        <th class="border px-4 py-2">カテゴリ</th>
                                        <th class="border px-4 py-2">状況</th>
                                        <th class="border px-4 py-2">依頼者</th>
                                        <th class="border px-4 py-2">場所</th>
                                        <th class="border px-4 py-2">日時</th>
                                        <th class="border px-4 py-2">アクション</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($requests->sortByDesc('date') as $request)
                                        <tr class="request-row"
                                            data-status="{{ $request->status_id }}"
                                            data-is-own="{{ $request->is_own ? 'true' : 'false' }}">
                                            <td class="border px-4 py-2">{{ $request->category3->category3 ?? '未設定' }}</td>
                                            <!-- ステータス -->
                                        <td class="border px-4 py-2">
                                            @php
                                                $statusLabels = [
                                                    1 => '新規依頼',
                                                    2 => '打ち合わせ中',
                                                    3 => 'マッチング確定',
                                                    4 => '終了',
                                                ];
                                            @endphp
                                            <span class="text-sm font-bold text-gray-800">{{ $statusLabels[$request->status_id] ?? '不明' }}</span>
                                        </td>
                                            <td class="border px-4 py-2">{{ $request->user->name ?? '不明' }}</td>
                                            <td class="border px-4 py-2">{{ $request->spot ?? '未設定' }}</td>
                                            <td class="border px-4 py-2">
                                                {{ \Carbon\Carbon::parse($request->date)->isoFormat('YYYY年MM月DD日（dddd）') }}
                                            </td>
                                            <td class="border px-4 py-2">
                                                <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700"
                                                    onclick="openModal({{ $request->id }})">詳細を見る</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p>依頼が登録されていません。</p>
                        @endif
                    </div>

                    <!-- モバイル表示用 -->
                    <div id="card-list" class="sm:hidden grid grid-cols-1 gap-4">
                        @foreach ($requests->sortByDesc('date') as $request)
                            <div class="request-card bg-gray-100 shadow-md rounded-lg p-4">
                                <p class="text-sm font-semibold text-gray-800">{{ $request->category3->category3 ?? '未設定' }}</p>
                                <p class="text-xs text-gray-600">
                                    {{ \Carbon\Carbon::parse($request->date)->isoFormat('YYYY年MM月DD日（dddd）') }}
                                </p>
                                <button class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-700 mt-2"
                                    onclick="openModal({{ $request->id }})">詳細を見る</button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

   <!-- モーダル -->
<div id="modal" class="fixed inset-0 bg-gray-600 bg-opacity-75 hidden flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg p-6 w-1/2">
        <h3 class="text-lg font-bold mb-4" id="modal-title">依頼詳細</h3>
        <p id="modal-category" class="text-sm mb-2">カテゴリ: </p>
        <p id="modal-user" class="text-sm mb-2">依頼者: </p>
        <p id="modal-location" class="text-sm mb-2">場所: </p>
        <p id="modal-datetime" class="text-sm mb-2">日時: </p>
        <p id="modal-status" class="text-sm mb-2">ステータス: </p>

        <!-- 打ち合わせボタン -->
        <div id="modal-action" class="mt-4">
            <!-- ボタンの内容は動的に設定 -->
        </div>

        <button class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-700 mt-4" onclick="closeModal()">閉じる</button>
    </div>
</div>


</x-app-layout>

<script>
    const requests = @json($requests);

    function filterRequests(filter) {
        const rows = document.querySelectorAll('.request-row');
        const cards = document.querySelectorAll('.request-card');

        [...rows, ...cards].forEach(element => {
            const isOwn = element.dataset.isOwn === 'true';
            const statusId = parseInt(element.dataset.status);

            if (filter === 'all') {
                element.style.display = 'table-row';
            } else if (filter === 'own' && isOwn) {
                element.style.display = 'table-row';
            } else if (filter === 'new' && statusId === 1) {
                element.style.display = 'table-row';
            } else if (filter === 'completed' && statusId === 4) {
                element.style.display = 'table-row';
            } else {
                element.style.display = 'none';
            }
        });
    }

    function openModal(requestId) {
    const modal = document.getElementById('modal');
    const request = requests.find(req => req.id === requestId);

    // モーダルにデータを挿入
    document.getElementById('modal-category').textContent = `カテゴリ: ${request.category3?.category3 ?? '未設定'}`;
    document.getElementById('modal-user').textContent = `依頼者: ${request.user?.name ?? '不明'}`;
    document.getElementById('modal-location').textContent = `場所: ${request.spot ?? '未設定'}`;
    document.getElementById('modal-datetime').textContent = `日時: ${request.date} ${request.time_start}`;
    document.getElementById('modal-status').textContent = `ステータス: ${request.status_name}`;

    // 打ち合わせボタンを生成
    const actionContainer = document.getElementById('modal-action');
    actionContainer.innerHTML = ''; // 既存のボタンをクリア

    if (request.can_join) {
        if (request.color === 'blue') {
            // 新規ルーム（青色ボタン）
            const form = document.createElement('form');
            form.action = `/supports/join/${request.id}`;
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700">
                    打ち合わせに参加
                </button>
            `;
            actionContainer.appendChild(form);
        } else if (request.color === 'orange') {
            // 自分のルーム（オレンジボタン）
            const link = document.createElement('a');
            link.href = `/meet_rooms/${request.id}`;
            link.className = 'bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-700';
            link.textContent = '自分のルーム';
            actionContainer.appendChild(link);
        }
    } else {
        // 定員オーバーまたは終了（グレーボタン）
        const button = document.createElement('button');
        button.className = 'bg-gray-300 text-gray-700 px-4 py-2 rounded cursor-not-allowed';
        button.disabled = true;
        button.textContent = request.status_id === 4 ? '終了' : 'マッチング不可';
        actionContainer.appendChild(button);
    }

    // モーダルを表示
    modal.classList.remove('hidden');
}

function closeModal() {
    const modal = document.getElementById('modal');
    modal.classList.add('hidden');
}

</script>
