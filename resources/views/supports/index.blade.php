<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('サポート - 依頼一覧') }}
        </h2>
    </x-slot>

    @if (isset($requests))
        <script>
            console.log('Requests:', @json($requests));
        </script>
    @else
        <p>データが渡されていません。</p>
    @endif

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-bold mb-4">依頼一覧</h3>

                    @if ($requests->isNotEmpty())
                        <table class="table-auto w-full border-collapse border border-gray-300">
                            <thead>
                                <tr>
                                    <th class="border border-gray-300 px-4 py-2">カテゴリ</th>
                                    <th class="border border-gray-300 px-4 py-2">依頼者</th>
                                    <th class="border border-gray-300 px-4 py-2">場所（めやす）</th>
                                    <th class="border border-gray-300 px-4 py-2">日時（調整可能）</th>
                                    <th class="border border-gray-300 px-4 py-2">マッチング</th>
                                    <th class="border border-gray-300 px-4 py-2">アクション</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($requests as $request)
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2">{{ $request->category3->category3 ?? '未設定' }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ $request->user->name ?? '不明' }}</td>
                                    <td class="border px-4 py-2">
                                        @php
                                            $spot = $request->spot ?? '未設定';
                                            $address1 = $request->user->address1 ?? '未設定';
                                        @endphp
                                        {{ $spot }} {{ $address1 !== '未設定' ? '(' . $address1 . ')' : '' }}
                                    </td>
                                    <td class="border px-4 py-2">
                                        @php
                                            try {
                                                $startDate = \Carbon\Carbon::parse($request->date);
                                                $startTime = $request->time_start
                                                    ? \Carbon\Carbon::createFromFormat('H:i:s', $request->time_start)->format('H:i')
                                                    : '未設定';
                                                $duration = $request->time ?? '未設定';
                                            } catch (\Exception $e) {
                                                $startDate = null;
                                                $startTime = '未設定';
                                                $duration = '未設定';
                                            }
                                        @endphp
                                        @if ($startDate)
                                            {{ $startDate->isoFormat('YYYY年MM月DD日（dddd）') }} {{ $startTime }}から{{ $duration }}時間
                                        @else
                                            日時情報が不正です
                                        @endif
                                        <td class="border px-4 py-2 text-center">
                                            <p class="text-sm text-blue-900">{{ $request->status_name }}</p>
                                            @if ($request->can_join)
                                            @if ($request->color === 'blue')
                                                <!-- 新規ルーム: 青色ボタン -->
                                                <form action="{{ route('supports.joinRoom', $request->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-700">
                                                        打ち合わせに参加
                                                    </button>
                                                </form>
                                            @elseif ($request->color === 'orange')
                                                <!-- 自分のルーム: オレンジボタン -->
                                                <a href="{{ route('meet_rooms.show', ['request_id' => $request->id]) }}" class="px-4 py-2 bg-orange-500 text-white rounded hover:bg-orange-700">
                                                    自分のルーム
                                                </a>
                                            @endif
                                        @else
                                            <!-- 定員オーバーまたは終了: グレーボタン -->
                                            <button class="px-4 py-2 bg-gray-300 text-gray-700 rounded cursor-not-allowed" disabled>
                                                {{ $request->status_id === 4 ? '終了' : 'マッチング' }}
                                            </button>
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
            </div>
        </div>
    </div>
</x-app-layout>
