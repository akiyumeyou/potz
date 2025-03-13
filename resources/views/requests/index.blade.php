<x-app-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
    $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
    @endphp
    @vite(['resources/js/thank.js'])

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('サポートの窓口') }}
        </h2>
    </x-slot>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-5">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-5 text-3xl text-green-900">
                @if ($membershipId >= 3 && $acId === 2)
                <a href="{{ route('supports.index') }}"
                class="inline-block bg-orange-400 text-white font-bold py-4 px-20 rounded-lg hover:bg-orange-500 text-lg">
                    {{ __('サポートに行く') }}
                </a>

            @elseif ($membershipId >= 3 && $acId !== 2)
                <p class="text-gray-500">
                    {{ __('プロフィールから認証画像の登録をしてください。') }}
                </p>
            @else
                <p class="text-gray-500">
                    {{ __('↓↓ボタンを押す') }}
                </p>
            @endif

            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-5">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-5 text-3xl text-green-900">
                @if ($requiresProfileCompletion)
                    <div class="text-red-500">
                        <p>登録を完了してください。</p>
                        <a href="{{ route('profile.edit') }}" class="text-blue-500 hover:underline">
                            会員区分を変更し登録を完了させる
                        </a>
                    </div>
                @else
                <a href="{{ route('requests.create') }}"
                class="inline-block bg-green-500 text-white font-bold py-4 px-20 rounded-lg hover:bg-orange-500 text-lg">
                {{ __('サポートを頼む') }}
            </a>

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
                                    <th class="px-4 py-2 border">サポート</th>
                                    <th class="px-4 py-2 border">すること</th>
                                    <th class="px-4 py-2 border">日時</th>
                                    <th class="px-4 py-2 border">場所</th>
                                    <th class="px-4 py-2 border">金額</th>
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
                                                1 => 'サポートさんをお待ちください',
                                                2 => '打ち合わせをして確定してください',
                                                3 => '当日をお待ちください',
                                                4 => '終了しました',
                                            ];
                                        @endphp
                                        <span class="text-sm font-bold text-gray-800">{{ $statusLabels[$request->status_id] ?? '不明' }}</span>
                                    </td>
                                    <td class="border px-4 py-2">
                                        {{ \Carbon\Carbon::parse($request->date)->isoFormat('YYYY年MM月DD日（dddd）') }}
                                        {{ \Carbon\Carbon::parse($request->time_start)->format('H:i') }}から
                    {{ rtrim($request->time, '.0') }}時間
                                    </td>
                                    <td class="border px-4 py-2">{{ $request->spot ?? '未指定' }}</td>
                                    <td class="border px-4 py-2 text-right">{{ number_format($request->estimate) }}円</td>
                                    <td class="border px-4 py-2 text-center">
                                        @if (in_array($request->status_id, [1, 2, 3]))
                                            <div class="flex items-center justify-start space-x-2">
                                                <!-- 打ち合わせボタン -->
                                                <a href="{{ route('meet_rooms.show', $request->id) }}"
                                                    class="bg-blue-500 text-white px-6 py-3 rounded-lg text-lg font-bold hover:bg-blue-600 focus:ring focus:ring-blue-300">
                                                    打ち合わせ
                                                </a>

                                                <!-- 未読件数（赤丸表示） -->
                                                @if ($request->unread_count > 0)
                                                    <span class="flex items-center justify-center bg-red-500 text-white text-sm font-bold w-6 h-6 rounded-full shadow">
                                                        {{ $request->unread_count }}
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <a href="{{ route('receipts.generatePdf', ['request_id' => $request->id]) }}"
                                                class="text-blue-500 underline hover:text-blue-700">
                                                領収書
                                            </a>

                                            <!-- ありがとうボタン -->
                                            <button
                                                class="thank-button flex items-center justify-center text-gray-700 border border-gray-300 rounded-lg px-4 py-2 {{ $request->is_liked ? 'bg-gray-200 cursor-not-allowed' : 'hover:bg-gray-100' }}"
                                                data-request-id="{{ $request->id }}"
                                                {{ $request->is_liked ? 'disabled' : '' }}
                                            >
                                                <span class="heart-icon mr-2">
                                                    {{ $request->is_liked ? '❤️' : '🤍' }}
                                                </span>
                                                {{ $request->is_liked ? 'ありがとう送信済' : 'ありがとうを送る' }}
                                            </button>
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
                                <p class="text-sm">{{ \Carbon\Carbon::parse($request->date)->isoFormat('YYYY年MM月DD日（dddd）') }} {{ \Carbon\Carbon::parse($request->time_start)->format('H:i') }}から
                                    {{ rtrim($request->time, '.0') }}時間</p>
                                <p class="text-sm">場所: {{ $request->spot ?? '未指定' }}</p>
                                <p class="text-sm">金額: {{ number_format($request->estimate) }}円</p>
                                <div class="flex justify-between mt-2">
                                     <!-- ステータス -->
                                     <div class="border px-4 py-2">
                                        @php
                                            $statusLabels = [
                                                1 => 'サポートさん探し中',
                                                2 => '確定してください',
                                                3 => '当日をお待ちください',
                                                4 => '終了しました',
                                            ];
                                        @endphp
                                        <span class="text-sm font-bold text-gray-800">{{ $statusLabels[$request->status_id] ?? '不明' }}</span>
                                     </div>
                                     <div class="relative">
                                        @if (in_array($request->status_id, [1, 2, 3]))
                                            <!-- 打ち合わせボタン -->
                                            <a href="{{ route('meet_rooms.show', $request->id) }}"
                                                class="bg-blue-500 text-white px-4 py-2 rounded-lg text-base font-bold hover:bg-blue-600 focus:ring focus:ring-blue-300">
                                                打ち合わせ
                                            </a>

                                            <!-- 未読件数（赤丸表示） -->
                                            @if ($request->unread_count > 0)
                                                <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold w-5 h-5 flex items-center justify-center rounded-full shadow">
                                                    {{ $request->unread_count }}
                                                </span>
                                            @endif
                                        @else
                                            <a href="{{ route('receipts.generatePdf', ['request_id' => $request->id]) }}"
                                                target="_blank"
                                                class="text-blue-500 underline hover:text-blue-700">
                                                領収書
                                            </a>

                                            <!-- ありがとうボタン -->
                                            <button
                                                class="thank-button flex items-center justify-center text-gray-700 border border-gray-300 rounded-lg px-4 py-2 {{ $request->is_liked ? 'bg-gray-200 cursor-not-allowed' : 'hover:bg-gray-100' }}"
                                                data-request-id="{{ $request->id }}"
                                                {{ $request->is_liked ? 'disabled' : '' }}
                                            >
                                                <span class="heart-icon mr-2">
                                                    {{ $request->is_liked ? '❤️' : '🤍' }}
                                                </span>
                                                {{ $request->is_liked ? 'ありがとう送信済' : 'ありがとうを送る' }}
                                            </button>
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
