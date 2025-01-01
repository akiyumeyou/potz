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
<!-- フィルタボタン -->
<div class="flex space-x-4 mb-6">
    <button id="filter-own" class="px-4 py-2 rounded text-white bg-green-500 hover:bg-green-700">
        自分の案件
    </button>
    <button id="filter-new" class="px-4 py-2 rounded text-white bg-yellow-500 hover:bg-yellow-700">
        新規案件
    </button>
    <button id="filter-all" class="px-4 py-2 rounded text-white bg-blue-500 hover:bg-blue-700">
        すべて
    </button>
    <button id="filter-completed" class="px-4 py-2 rounded text-white bg-gray-500 hover:bg-gray-700">
        終了
    </button>
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
                                        <th class="border px-4 py-2">サポーター</th>
                                        <th class="border px-4 py-2">日時</th>
                                        <th class="border px-4 py-2">アクション</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($requests->sortByDesc('date') as $request)
                                    <tr class="request-row"
                                    data-is-own="{{ $request->supporter_id === $user->id ? 'true' : 'false' }}"
                                    data-status="{{ $request->status_id }}">

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
                                            <td class="border px-4 py-2">{{ $request->supporter->name ?? '未割り当て' }}</td>
                                            <td class="border px-4 py-2">
                                                {{ \Carbon\Carbon::parse($request->date)->isoFormat('YYYY年MM月DD日（dddd）') }}
                                            </td>
                                            <td class="border px-4 py-2">
                                                <!-- 詳細ボタン -->
                                                <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700"
                                                    onclick="openModal({{ $request->id }})">詳細を見る</button>

                                                <!-- 打ち合わせ -->
                                                <!-- サポートするボタン -->
                                                @if ($request->status_id === 1)
                                                <form action="{{ route('support.joinRoom', $request->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-700">
                                                        サポートする
                                                    </button>
                                                </form>
                                                @elseif ($request->can_join)
                                                <!-- 打ち合わせに入るボタン -->
                                                <form action="{{ route('support.joinRoom', $request->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700">
                                                        打ち合わせに入る
                                                    </button>
                                                </form>
                                                @endif


                                                <!-- 再依頼ボタン -->
                                                @if ($request->can_recreate)
                                                    <a href="{{ route('requests.createFromRequest', ['from_request' => $request->id]) }}"
                                                       class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-700">
                                                        再依頼
                                                    </a>
                                                @endif
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
                        <div class="request-card bg-gray-100 shadow-md rounded-lg p-4"
                        data-is-own="{{ $request->supporter_id === $user->id ? 'true' : 'false' }}"
                        data-status="{{ $request->status_id }}">
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

        <!-- ボタンを表示 -->
        <div id="modal-action" class="mt-4">
            <!-- 動的に内容を設定 -->
        </div>

        <button class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-700 mt-4" onclick="closeModal()">閉じる</button>
    </div>
</div>


</x-app-layout>

<script>
    const requests = @json($requests);

   // フィルタリング機能
   document.addEventListener('DOMContentLoaded', () => {
        const rows = document.querySelectorAll('.request-row');

        // フィルタボタンのクリックイベント
        document.getElementById('filter-own').addEventListener('click', () => {
            filterRequests('own');
        });
        document.getElementById('filter-new').addEventListener('click', () => {
            filterRequests('new');
        });
        document.getElementById('filter-all').addEventListener('click', () => {
            filterRequests('all');
        });
        document.getElementById('filter-completed').addEventListener('click', () => {
            filterRequests('completed');
        });

        // フィルタ処理
        function filterRequests(filter) {
    rows.forEach(row => {
        const isOwn = row.dataset.isOwn === 'true';
        const statusId = parseInt(row.dataset.status);

        // デスクトップ用フィルタ
        if (filter === 'own' && isOwn) {
            row.style.display = 'table-row';
        } else if (filter === 'new' && statusId === 1) {
            row.style.display = 'table-row';
        } else if (filter === 'all') {
            row.style.display = 'table-row';
        } else if (filter === 'completed' && statusId === 4) {
            row.style.display = 'table-row';
        } else {
            row.style.display = 'none';
        }
    });

    // モバイル用カードリストフィルタ
    const cards = document.querySelectorAll('.request-card');
    cards.forEach(card => {
        const isOwn = card.dataset.isOwn === 'true';
        const statusId = parseInt(card.dataset.status);

        if (filter === 'own' && isOwn) {
            card.style.display = 'block';
        } else if (filter === 'new' && statusId === 1) {
            card.style.display = 'block';
        } else if (filter === 'all') {
            card.style.display = 'block';
        } else if (filter === 'completed' && statusId === 4) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}


        // 初期状態で「自分の案件」を表示
        filterRequests('own');
    });

    function openModal(requestId) {
    const modal = document.getElementById('modal');
    const request = requests.find(req => req.id === requestId);

    // モーダルにデータを挿入
    document.getElementById('modal-category').textContent = `カテゴリ: ${request.category3?.category3 ?? '未設定'}`;
    document.getElementById('modal-user').textContent = `依頼者: ${request.user?.name ?? '不明'}`;
    document.getElementById('modal-location').textContent = `場所: ${request.spot ?? '未設定'}`;
    document.getElementById('modal-datetime').textContent = `日時: ${request.date} ${request.time_start}`;
    document.getElementById('modal-status').textContent = `ステータス: ${request.status_name}`;

    const actionContainer = document.getElementById('modal-action');
    actionContainer.innerHTML = ''; // 既存のボタンをクリア

    if (request.can_join) {
        // 打ち合わせに入るボタン
        const joinButton = document.createElement('a');
        joinButton.href = `/meet_rooms/${request.id}`;
        joinButton.className = 'bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700';
        joinButton.textContent = '打ち合わせに入る';
        actionContainer.appendChild(joinButton);
    }

    if (request.can_recreate) {
        // 再依頼ボタン
        const recreateButton = document.createElement('a');
        recreateButton.href = `/requests/create-from-request/${request.id}`;
        recreateButton.className = 'bg-green-500 text-white px-4 py-2 rounded hover:bg-green-700';
        recreateButton.textContent = '再依頼';
        actionContainer.appendChild(recreateButton);
    }

    modal.classList.remove('hidden');
}

function closeModal() {
    const modal = document.getElementById('modal');
    modal.classList.add('hidden');
}

</script>
