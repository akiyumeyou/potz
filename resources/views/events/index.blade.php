<x-app-layout>

    <style>
        .btn-orange {
            background-color: #FFA500; /* オレンジ色 */
            display: inline-block;
            transition: background-color 0.3s;
        }

        .btn-orange:hover {
            background-color: #FF8C00; /* オレンジ色のホバー状態 */
        }

        /* 全体の文字サイズを16pxに設定 */
        body {
            font-size: 16px;
        }
    </style>

    <body class="bg-gray-100">
        <div class="container mx-auto px-4 py-8">
            <h1 class="text-3xl font-bold mb-6">イベント一覧</h1>

            @if (session('success'))
                <div class="alert alert-success bg-green-100 text-green-800 p-4 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <a href="{{ route('events.create') }}" class="inline-block mb-6 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">イベントを作成</a>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($events as $event)
                    <div class="bg-white shadow-md rounded-lg p-6">
                        <h2 class="text-xl font-bold mb-2">{{ $event->title }}</h2>
                        @if($event->image_path)
                        <img src="{{ $event->getImageUrl() }}" alt="イベント画像" class="mb-4 rounded-lg">
                    @endif
                        <p class="mb-1">開催日: {{ $event->getDisplayEventDate() }}</p>
                        <p class="mb-1">時間: {{ $event->start_time }} - {{ $event->end_time }}</p>
                        <p class="mb-1">内容: {{ $event->content }}</p>
                        <p class="mb-1">作成者: {{ $event->user->name }}</p>
                        <p class="mb-4">参加費: 無料</p>

                        @if($event->isOngoing())
                            <a href="{{ $event->zoom_url }}" class="btn-orange bg-orange-500 text-white font-bold py-2 px-4 rounded hover:bg-orange-700">参加</a>
                        @elseif($event->recurring && !$event->holiday)
                            <p>次回開催日: {{ $event->getNextEventDate() }}</p>
                            <button class="bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-700">準備中</button>
                        @elseif($event->isUpcoming())
                            <button class="bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-700">準備中</button>
                        @else
                            <button class="bg-gray-500 text-white font-bold py-2 px-4 rounded" disabled>終了しました</button>
                        @endif

                        @if(Auth::id() === $event->user_id)
                            <div class="mt-4">
                                <a href="{{ route('events.edit', $event) }}" class="text-blue-500 hover:underline mr-2">編集</a>
                                <form action="{{ route('events.destroy', $event) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:underline">削除</button>
                                </form>
                            </div>
                        @endif
                    </div>
                @empty
                    <p class="col-span-full text-center">現在、イベントはありません。</p>
                @endforelse
            </div>
        </div>
    </body>
</x-app-layout>
