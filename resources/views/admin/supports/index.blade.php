<x-app-layout>
    <x-slot name="header">
        <h2>サポート一覧 (管理者用)</h2>
    </x-slot>

    <div class="container mx-auto mt-4">
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
                            6 => 'キャンセル',
                            9 => '削除',
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
                        {{ optional($support->requester)->name ?? '未設定' }}
                    </td>

                    <!-- サポーター名 -->
                    <td class="border px-4 py-2">
                        {{ optional($support->supporter)->name ?? '未設定' }}
                    </td>

                    <!-- 場所 -->
                    <td class="border px-4 py-2">
                        {{ $support->spot ?? '未設定' }}
                    </td>

                    <!-- 操作 -->
                    <td>
                        <a href="{{ route('admin.supports.edit', $support->id) }}" class="text-blue-500 hover:underline">編集</a>
                        <a href="{{ route('admin.supports.meet', $support->id) }}" class="text-blue-500 hover:underline">打ち合わせ</a>

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
