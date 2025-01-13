<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('サポート') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-5">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-5 text-3xl text-green-900">
                @if ($requiresProfileCompletion)
                    <div class="text-red-500">
                        <p>プロフィール登録を完了してください。</p>
                        <a href="{{ route('profile.edit') }}" class="text-blue-500 hover:underline">
                            プロフィールを登録する
                        </a>
                    </div>
                @else
                    <a href="{{ route('requests.create') }}" class="text-blue-500 hover:underline">
                        {{ __('ちょっと助けて依頼') }}
                    </a>
                @endif
            </div>
        </div>
    </div>


<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-5">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-5 text-3xl text-green-900">
            @if ($membershipId >= 3 && $acId === 2)
            <a href="{{ route('supports.index') }}" class="text-orange-500 hover:underline">
                {{ __('サポート検索') }}
            </a>

        @elseif ($membershipId >= 3 && $acId !== 2)
            <p class="text-gray-500">
                {{ __('プロフィールから認証画像の登録をしてください。') }}
            </p>
        @else
            <p class="text-gray-500">
                {{ __('依頼のみできます') }}
            </p>
        @endif

        </div>
    </div>
</div>

    <!-- 依頼一覧 -->
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-bold mb-4">依頼一覧</h3>

                    <!-- レスポンシブ対応 -->
                    <div class="hidden sm:block">
                        <table class="table-auto w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="px-4 py-2 border">カテゴリ</th>
                                    <th class="px-4 py-2 border">状況</th>
                                    <th class="px-4 py-2 border">日時</th>
                                    <th class="px-4 py-2 border">場所</th>
                                    <th class="px-4 py-2 border">見込み金額</th>
                                    <th class="px-4 py-2 border">打ち合わせ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($requests as $request)
                                    <tr>
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
                                    <td class="border px-4 py-2">
                                        {{ \Carbon\Carbon::parse($request->date)->isoFormat('YYYY年MM月DD日（dddd）') }}
                                        {{ $request->time_start ?? '' }}から{{ $request->time ?? '' }}時間
                                    </td>
                                    <td class="border px-4 py-2">{{ $request->spot ?? '未指定' }}</td>
                                    <td class="border px-4 py-2 text-right">{{ $request->estimate ? ceil($request->estimate) . '円' : '未指定' }}</td>
                                    <td class="border px-4 py-2 text-center">
                                        @if (in_array($request->status_id, [1, 2, 3]))
                                        <div class="flex items-center justify-start space-x-2">
                                            <!-- ボタン -->
                                            <a href="{{ route('meet_rooms.show', $request->id) }}"
                                               class="bg-blue-500 text-white px-6 py-3 rounded text-lg font-bold hover:bg-blue-600">
                                                打ち合わせ
                                            </a>

                                            <!-- 未読件数 (赤丸) -->
                                            @if ($request->unread_count > 0)
                                                <span class="flex items-center justify-center bg-red-500 text-white text-sm font-bold w-6 h-6 rounded-full">
                                                    {{ $request->unread_count }}
                                                </span>
                                            @endif
                                        </div>
                                        @else
                                        <a href="{{ route('receipts.generatePdf', ['request_id' => $request->id]) }}"
                                            class="text-blue-500 underline hover:text-blue-700">
                                             領収書参照
                                        </a>
                                        @endif
                                    </td>

                                        <!-- <td class="border px-4 py-2 text-center">
                                            <a href="{{ route('requests.createFromRequest', ['from_request' => $request->id]) }}" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                                                再依頼
                                            </a>
                                        </td> -->
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- モバイル対応（カード形式） -->
                    <div class="sm:hidden space-y-4">
                        @foreach ($requests as $request)
                            <div class="border rounded p-4 shadow">
                                <p class="text-lg font-bold">{{ $request->category3->category3 ?? '未設定' }}</p>
                                <p class="text-sm">{{ \Carbon\Carbon::parse($request->date)->isoFormat('YYYY年MM月DD日（dddd）') }} {{ $request->time_start ?? '' }}から{{ $request->time ?? '' }}時間</p>
                                <p class="text-sm">場所: {{ $request->spot ?? '未指定' }}</p>
                                <p class="text-sm">見込み金額: {{ $request->estimate ? ceil($request->estimate) . '円' : '未指定' }}</p>
                                <div class="flex justify-between mt-2">
                                    <!-- 打ち合わせボタン -->
                                    <div class="relative">
                                        <a href="{{ route('meet_rooms.show', $request->id) }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                                            打ち合わせ
                                        </a>
                                        @if ($request->unread_count > 0)
                                            <!-- 未読件数（赤丸） -->
                                            <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold w-5 h-5 flex items-center justify-center rounded-full">
                                                {{ $request->unread_count }}
                                            </span>
                                        @endif
                                    </div>

                                    <!-- <a href="{{ route('requests.create', ['from_request' => $request->id]) }}" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                                        再依頼
                                    </a> -->
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if ($requests->isEmpty())
                        <p class="mt-4 text-gray-500">まだ依頼が登録されていません。</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
