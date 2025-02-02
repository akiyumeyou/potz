<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight">
            有料イベント一覧
        </h2>
    </x-slot>

    <div class="container mx-auto p-4">
        @if (session('success'))
            <div class="alert alert-success bg-green-100 text-green-800 p-4 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif
        <a href="{{ route('admin.events.create') }}"
        class="text-white bg-orange-500 hover:bg-orange-400 px-6 py-3 rounded-lg shadow-lg text-lg font-bold">
        イベントを作成
        </a>
        @if ($paidEvents->isEmpty())
            <p class="text-gray-600 text-center">現在、有料イベントはありません。</p>
        @else
            <table class="table-auto w-full border-collapse border border-gray-200 mt-4">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-300 px-4 py-2">タイトル</th>
                        <th class="border border-gray-300 px-4 py-2">開催日</th>
                        <th class="border border-gray-300 px-4 py-2">参加費</th>
                        <th class="border border-gray-300 px-4 py-2">参加者数</th>
                        <th class="border border-gray-300 px-4 py-2">詳細</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($paidEvents as $event)
                        <tr>
                            <td class="border border-gray-300 px-4 py-2">{{ $event->title }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $event->event_date }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ number_format($event->price) }}円</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $event->participants->count() }}</td>
                            <td class="border border-gray-300 px-4 py-2">
                                <a href="{{ route('admin.events.participants', $event->id) }}"
                                   class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700">
                                    参加者確認
                                </a>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-6">
                {{ $paidEvents->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
