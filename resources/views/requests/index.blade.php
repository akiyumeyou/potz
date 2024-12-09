<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('ダッシュボード') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-5">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-5 text-3xl text-green-900">
                <a href="{{ route('requests.create') }}" class="text-blue-500 hover:underline">
                    {{ __('ちょっと助けて依頼') }}
                </a>
            </div>
        </div>
    </div>

    <!-- @if ($membershipId === 3 && $acId === 2)
    <a href="{{ route('supports.index') }}" class="text-blue-500 hover:underline">
        サポート検索
    </a>
@else
    <p class="text-gray-500">サポーター区分ではありません。</p>
@endif

    @if (auth()->check() && optional($user->profile)->membership_id === 3)
    <a href="{{ route('supports.index') }}" class="text-blue-500 hover:underline">
        サポート検索
    </a>
@endif -->
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-5">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-5 text-3xl text-green-900">
            @if ($membershipId === 3 && $acId === 2)
                <a href="{{ route('supports.index') }}" class="text-orange-500 hover:underline">
                    {{ __('サポート検索') }}
                </a>
            @elseif ($membershipId === 3 && $acId !== 2)
                <p class="text-gray-500">
                    {{ __('プロフィールから活動認証の登録をしてください。') }}
                </p>
            @else
                <p class="text-gray-500">
                    {{ __('依頼のみできます') }}
                </p>
            @endif
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
                                <th class="px-4 py-2">見込み金額</th>
                                <th class="px-4 py-2">打ち合わせ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($requests as $request)
                                <tr>
                                    <!-- カテゴリ -->
                                    <td class="border px-4 py-2">
                                        {{ $request->category3->category3 ?? '未設定' }}
                                    </td>

                                    <!-- 日時 -->
                                    <td class="border px-4 py-2">
                                        @php
                                            // 日付と時刻を処理
                                            try {
                                                $startDate = \Carbon\Carbon::parse($request->date);
                                                $startTime = $request->time_start
                                                    ? \Carbon\Carbon::createFromFormat('H:i:s', $request->time_start)->format('H:i')
                                                    : '未設定'; // time_start が null または不正な形式の場合
                                                $duration = $request->time ?? '未設定'; // 作業時間
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
                                    </td>

                                    <!-- 場所 -->
                                    <td class="border px-4 py-2">
                                        {{ $request->spot ?? '未指定' }}
                                    </td>

                                    <!-- 見積もり -->
                                    <td class="border px-4 py-2 text-right">
                                        {{ $request->estimate ? ceil($request->estimate) . '円' : '未指定' }}
                                    </td>

                                    <!-- 打ち合わせ -->
                                    <td class="border px-4 py-2 text-center">
                                        <p class="text-sm text-gray-500">{{ $request->status_name }}</p>
                                        @if (in_array($request->status_id, [1, 2, 3]))
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
