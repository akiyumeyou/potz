<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('サポート - 依頼一覧') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
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
                                        <!-- <th class="border px-4 py-2">サポーター</th> -->
                                        <th class="border px-4 py-2">日</th>
                                        <th class="border px-4 py-2">時</th>
                                        <th class="border px-4 py-2">予定額</th>
                                        <th class="border px-4 py-2">依頼者</th>
                                        <th class="border px-4 py-2">距離</th>
                                        <th class="border px-4 py-2">内容</th>
                                        <th class="border px-4 py-2 w-48">アクション</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($requests->sortByDesc('date') as $request)
                                    <tr class="request-row"
                                        data-is-own="{{ $request->supporter_id === $user->id ? 'true' : 'false' }}"
                                        data-status="{{ $request->status_id }}">
                                        <td class="border px-4 py-2">{{ $request->category3->category3 ?? '未設定' }}</td>
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
                                        <!-- <td class="border px-4 py-2">{{ $request->supporter->name ?? '未割り当て' }}</td> -->
                                        <td class="border px-4 py-2">
                                            {{ \Carbon\Carbon::parse($request->date)->isoFormat('YYYY年MM月DD日（dddd）') }}
                                        </td>
                                        <td class="border px-4 py-2">{{ \Carbon\Carbon::parse($request->time_start)->format('H:i') ?? '未設定' }}（{{ $request->time }}時間）</td>
                                        <td class="border px-4 py-2">¥{{ number_format($request->estimate) }}</td>
                                        <td class="border px-4 py-2">
                                            {{ $request->user->name ?? '不明' }}（{{ $request->user->gender === 'male' ? '男性' : ($request->user->gender === 'female' ? '女性' : 'その他') }}・{{ $request->user->age ?? '不明' }}歳・{{ $request->user->address1 ?? '不明' }}）
                                        </td>

                                        <td class="border px-4 py-2">{{ $request->distance }} km</td>
                                        <td class="border px-4 py-2">{{ $request->contents }}</td>
                                        <td class="border px-4 py-2 relative">
                                            <!-- サポートするボタン -->
                                            @if ($request->status_id === 1)
                                            <form action="{{ route('support.joinRoom', ['id' => $request->id]) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-700">
                                                    サポートする
                                                </button>
                                            </form>
                                            @endif

                                           <!-- 打ち合わせボタン -->
        @if ($request->status_id === 2 || $request->status_id === 3)
        <div class="relative">
            <a href="{{ route('meet_rooms.show', ['request_id' => $request->id]) }}"
               class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-700 mb-2 inline-block">
                打ち合わせ
            </a>
            @if ($request->unread_count > 0)
            <!-- 未読件数（赤丸） -->
            <span class="absolute top-0 right-0 transform translate-x-1/2 -translate-y-1/2 bg-red-500 text-white text-xs font-bold w-6 h-6 flex items-center justify-center rounded-full">
                {{ $request->unread_count }}
            </span>
            @endif
        </div>
        @endif

        <!-- 再依頼ボタン -->
        @if ($request->status_id === 3 || $request->status_id === 4)
        <a href="{{ route('requests.createFromRequest', ['from_request' => $request->id]) }}"
           class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-700 inline-block">
            代理再依頼
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
        <p class="text-sl font-semibold text-gray-800">{{ $request->category3->category3 ?? '未設定' }}</p>
        <p>
            {{ \Carbon\Carbon::parse($request->date)->isoFormat('YYYY年MM月DD日（dddd）') }}
        </p>
        <p>開始: {{ \Carbon\Carbon::parse($request->time_start)->format('H:i') ?? '未設定' }}（{{ $request->time }}時間）</p>
        <p>予定額: ¥{{ number_format($request->estimate) }}</p>
        <p>依頼者:
            {{ $request->user->name ?? '不明' }}（{{ $request->user->gender === 'male' ? '男性' : ($request->user->gender === 'female' ? '女性' : 'その他') }}・{{ $request->user->age ?? '不明' }}歳・{{ $request->user->address1 ?? '不明' }}）
        </p>
        <p>あなたとの距離: {{ $request->distance }} km</p>
        <p>{{ $request->contents }}</p>
        <!-- ボタンエリア -->
        <div class="flex space-x-4 mt-4">
            <!-- サポートするボタン -->
            @if ($request->status_id === 1)
            <form action="{{ route('support.joinRoom', ['id' => $request->id]) }}" method="POST">
                @csrf
                <button type="submit" class="bg-orange-500 text-white py-2 px-4 rounded hover:bg-orange-700">
                    サポートする
                </button>
            </form>
            @endif

      <!-- 打ち合わせに入るボタン -->
      <td class="border px-4 py-2 relative">
        <div class="flex items-center space-x-2">
            <!-- 打ち合わせボタン -->
            @if ($request->status_id === 2 || $request->status_id === 3)
            <div class="relative">
                <a href="{{ route('meet_rooms.show', ['request_id' => $request->id]) }}"
                   class="bg-blue-500 text-white px-8 py-3 rounded-lg hover:bg-blue-700 block text-center">
                    打ち合わせ
                </a>
                @if ($request->unread_count > 0)
                <!-- 未読件数（赤丸） -->
                <span class="absolute top-0 right-0 transform translate-x-1/2 -translate-y-1/2 bg-red-500 text-white text-xs font-bold w-5 h-5 flex items-center justify-center rounded-full">
                    {{ $request->unread_count }}
                </span>
                @endif
            </div>
            @endif

            <!-- 再依頼ボタン -->
            @if ($request->status_id === 3 || $request->status_id === 4)
            <a href="{{ route('requests.createFromRequest', ['from_request' => $request->id]) }}"
               class="bg-green-500 text-white py-3 px-8 rounded-lg hover:bg-green-700 block text-center">
                代理再依頼
            </a>
            @endif
        </div>
    </td>


        </div>
    </div>
    @endforeach
</div>


                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>


<script>
    const requests = @json($requests);

   // フィルタリング機能
   document.addEventListener('DOMContentLoaded', () => {
        const rows = document.querySelectorAll('.request-row');

    // フィルタボタンのクリックイベント
    document.getElementById('filter-own').addEventListener('click', () => filterRequests('own'));
    document.getElementById('filter-new').addEventListener('click', () => filterRequests('new'));
    document.getElementById('filter-completed').addEventListener('click', () => filterRequests('completed'));

        // フィルタ処理
        function filterRequests(filter) {
    rows.forEach(row => {
        const isOwn = row.dataset.isOwn === 'true';
        const statusId = parseInt(row.dataset.status);

        // デスクトップ用フィルタ
        if (filter === 'own' && isOwn && [2, 3].includes(statusId)) {
                row.style.display = 'table-row';
            } else if (filter === 'new' && statusId === 1) {
                row.style.display = 'table-row';
            } else if (filter === 'completed' && isOwn && statusId === 4) {
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

            if (filter === 'own' && isOwn && [2, 3].includes(statusId)) {
                card.style.display = 'block';
            } else if (filter === 'new' && statusId === 1) {
                card.style.display = 'block';
            } else if (filter === 'completed' && isOwn && statusId === 4) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
}


        // 初期状態で「自分の案件」を表示
        filterRequests('own');
    });
    </script>
