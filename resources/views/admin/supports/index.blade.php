<x-app-layout>
    <x-slot name="header">
        <h2>サポート一覧 (管理者用)</h2>
    </x-slot>

    <div class="container mx-auto mt-4">

        <!-- フィルタボタン -->
        <div class="mb-4 flex space-x-4">
            <a href="{{ route('admin.supports.index', ['filter' => 'all']) }}"
               class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-700 {{ request('filter') === 'all' || !request('filter') ? 'bg-gray-700' : '' }}">
                すべて
            </a>
            <a href="{{ route('admin.supports.index', ['filter' => 'new']) }}"
               class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-700 {{ request('filter') === 'new' ? 'bg-yellow-700' : '' }}">
                新規案件
            </a>
            <a href="{{ route('admin.supports.index', ['filter' => 'in_progress']) }}"
               class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-700 {{ request('filter') === 'in_progress' ? 'bg-green-700' : '' }}">
                調整中
            </a>
            <a href="{{ route('admin.supports.index', ['filter' => 'completed']) }}"
               class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700 {{ request('filter') === 'completed' ? 'bg-blue-700' : '' }}">
                終了
            </a>
            <a href="{{ route('admin.supports.index', ['filter' => 'cancelled']) }}"
               class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-700 {{ request('filter') === 'cancelled' ? 'bg-red-700' : '' }}">
                キャンセル
            </a>
        </div>

        <!-- サポート一覧 -->
        <table class="table-auto border-collapse w-full">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>ステータス</th>
                    <th>カテゴリ</th>
                    <th>日時</th>
                    <th>依頼者名</th>
                    <th>サポーター名</th>
                    <th>場所</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($supports as $support)
                <tr>
                    <!-- ID -->
                    <td>{{ $support->id }}</td>

                    <!-- ステータス -->
                    <td>
                        {{ match ($support->status_id) {
                            1 => '準備中',
                            2 => '調整中',
                            3 => '確定',
                            4 => '完了',
                            5 => 'キャンセル',
                            default => '不明'
                        } }}
                    </td>

                    <!-- カテゴリ -->
                    <td class="border px-4 py-2">
                        {{ optional($support->category3)->category3 ?? '未設定' }}
                    </td>

                    <!-- 日時 -->
                    <td class="border px-4 py-2">
                        {{ \Carbon\Carbon::parse($support->date)->isoFormat('YYYY年MM月DD日（dddd）') }}
                    </td>

                    <!-- 依頼者名 -->
                    <td class="border px-4 py-2">
                        {{ optional($support->user)->real_name ?? '未設定' }}
                    </td>

                    <!-- サポーター名 -->
                    <td class="border px-4 py-2">
                        {{ optional($support->supporter)->real_name ?? '未設定' }}
                    </td>


                    <!-- 場所 -->
                    <td class="border px-4 py-2">
                        {{ $support->spot ?? '未設定' }}
                    </td>

                    <!-- 操作 -->
                    <td>
                        <a href="{{ route('admin.supports.edit', $support->id) }}" class="text-blue-500 hover:underline">編集</a>
                        <a href="{{ route('admin.supports.meet', $support->id) }}" class="text-blue-500 hover:underline">打ち合わせ</a>
                        <form action="{{ route('admin.supports.destroy', $support->id) }}" method="POST" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:underline" onclick="return confirm('本当に削除しますか？')">削除</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            {{ $supports->links() }}
        </div>
    </div>
</x-app-layout>
