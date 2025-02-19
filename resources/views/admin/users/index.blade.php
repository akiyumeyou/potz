<x-app-layout>
    <x-slot name="header">
        <h2>ユーザー一覧</h2>
    </x-slot>

    <div class="container mx-auto mt-4">
        <!-- メール送信対象選択フォーム -->
        <form method="POST" action="{{ route('admin.users.send_email') }}">
            @csrf

    <!-- 送信モードの選択 -->
    <div class="mb-4">
        <label for="send_mode" class="mr-2 font-bold">送信モード:</label>
        <select name="send_mode" id="send_mode" class="border rounded p-1">
            <option value="selected" selected>画面上で選択したユーザーに送信</option>
            <option value="group">会員区分で全て送信</option>
        </select>
    </div>

    <!-- 会員区分フィルター（グループ送信用） -->
    <div id="membership_filter_group" class="mb-4" style="display: none;">
        <label for="membership_filter" class="mr-2 font-bold">送信対象の会員区分:</label>
        <select id="membership_filter" name="membership_filter" class="border rounded p-1">
            <option value="1">一般会員</option>
            <option value="2">POTZ会員</option>
            <option value="3">サポート会員</option>
        </select>
    </div>

            <!-- 送信ボタン -->
            <div class="mt-4">
                <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">メール送信</button>
            </div>
            <!-- ユーザー一覧テーブル（各行にチェックボックスを追加） -->
            <table class="table-auto border-collapse w-full">
                <thead>
                    <tr>
                        <th class="border-b">
                            <!-- 全選択用チェックボックス -->
                            <input type="checkbox" id="select-all">
                        </th>
                        <th class="border-b">ID</th>
                        <th class="border-b">会員区分</th>
                        <th class="border-b">名前</th>
                        <th class="border-b">メールアドレス</th>
                        <th class="border-b">操作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                    <tr class="border-b">
                        <td class="py-2">
                            <input type="checkbox" name="selected_users[]" value="{{ $user->id }}" class="user-checkbox">
                        </td>
                        <td class="py-2">{{ $user->id }}</td>
                        <td class="py-2">{{ optional($user->membershipClass)->m_name ?? '未設定' }}</td>
                        <td class="py-2">{{ $user->name }}</td>
                        <td class="py-2">{{ $user->email }}</td>
                        <td class="py-2">
                            @if ($user->membership_id == 3 && optional($user->supporterProfile)->ac_id == 1)
                                <a href="{{ route('admin.users.show', $user->id) }}"
                                   class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">未認証</a>
                            @elseif ($user->membership_id == 3 && optional($user->supporterProfile)->ac_id == 2)
                                <a href="{{ route('admin.users.show', $user->id) }}"
                                   class="px-4 py-2 bg-orange-500 text-white rounded hover:bg-orange-600">認証済</a>
                            @else
                                <a href="{{ route('admin.users.show', $user->id) }}"
                                   class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">詳細</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </form>

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>

   <!-- JavaScript: 送信モードに応じて会員区分フィルターの表示切替 -->
<script>
    const sendModeSelect = document.getElementById('send_mode');
    const membershipFilterGroup = document.getElementById('membership_filter_group');

    sendModeSelect.addEventListener('change', function() {
        if (this.value === 'group') {
            membershipFilterGroup.style.display = 'block';
        } else {
            membershipFilterGroup.style.display = 'none';
        }
    });

    // 全選択チェックボックスの処理
    document.getElementById('select-all').addEventListener('change', function() {
        var checkboxes = document.querySelectorAll('.user-checkbox');
        checkboxes.forEach(function(checkbox) {
            checkbox.checked = document.getElementById('select-all').checked;
        });
    });
</script>
</x-app-layout>
