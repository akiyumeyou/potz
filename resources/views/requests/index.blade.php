<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('ダッシュボード') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-5 text-3xl text-green-900">
                <a href="{{ route('requests.create') }}" class="text-blue-500 hover:underline">
                    {{ __('ちょっと助けて依頼') }}
                </a>
            </div>
        </div>
    </div>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-bold mb-4">依頼一覧</h3>

                    <table class="table-auto w-full text-left">
                        <thead>
                            <tr>
                                <th class="px-4 py-2">カテゴリ</th>
                                <th class="px-4 py-2">日時</th>
                                <th class="px-4 py-2">場所</th>
                                <th class="px-4 py-2">打ち合わせ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($requests as $request)
                                <tr>
                                    <!-- カテゴリ -->
                                    <td class="border px-4 py-2">
                                        {{ optional($request->category)->name ?? '未設定' }}
                                    </td>

                                    <!-- 日時 -->
                                    <td class="border px-4 py-2">
                                        @php
                                            $start = \Carbon\Carbon::parse($request->date);
                                            $duration = $request->duration ?? 1; // デフォルト1時間
                                        @endphp
                                        {{ $start->isoFormat('YYYY年MM月DD日（dddd）') }} {{ $start->hour }}時から{{ $duration }}時間
                                    </td>

                                    <!-- 場所 -->
                                    <td class="border px-4 py-2">
                                        {{ $request->spot ?? '未指定' }}
                                    </td>

                                    <!-- 打ち合わせ -->
                                    <td class="border px-4 py-2 text-center">
                                        <!-- ステータス名の表示 -->
                                        <p class="text-sm text-gray-500">: {{ $request->status_name }}</p>

                                        <!-- 打ち合わせボタン -->
                                        @if (in_array($request->status_id, [1, 2])) <!-- ステータスが準備中または調整中の場合 -->
                                            <a href="{{ route('meet_rooms.show', $request->id) }}" class="bg-blue-500 text-white px-4 py-2 rounded">
                                                打ち合わせ
                                            </a>
                                        @else
                                            <span class="text-gray-400">利用不可</span>
                                        @endif
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if ($requests->isEmpty())
                        <p class="mt-4 text-gray-500">まだ依頼が登録されていません。</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
