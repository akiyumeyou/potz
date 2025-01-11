<x-app-layout>
    <x-slot name="header">
        <h2>ユーザー一覧</h2>
    </x-slot>

    <div class="container mx-auto mt-4">
        <table class="table-auto border-collapse w-full">
            <thead>
                <tr>
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
                    <td class="py-2">{{ $user->id }}</td>
                    <td class="py-2">{{ optional($user->membershipClass)->m_name ?? '未設定' }}</td>
                    <td class="py-2">{{ $user->name }}</td>
                    <td class="py-2">{{ $user->email }}</td>
                    <td class="py-2">
                        @if ($user->membership_id == 3 && optional($user->supporterProfile)->ac_id == 1)
                <a href="{{ route('admin.users.show', $user->id) }}"
                   class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">認証</a>
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

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>
</x-app-layout>
