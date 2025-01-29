<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('オンライン開催予定') }}
            </h2>
            <a href="{{ route('dashboard') }}"
               class="px-4 py-2 bg-blue-900 text-white text-sm font-bold rounded shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-gray-300">
                戻る
            </a>
        </div>
    </x-slot>

    <body class="bg-gray-100">
        <div class="container mx-auto px-4 py-1">
            @if (session('success'))
                <div class="alert alert-success bg-green-100 text-green-800 p-4 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <header class="mb-10">
                <nav class="mt-4 flex justify-end pr-4">
                    @if (Auth::check() && Auth::user()->membership_id >= 3)
                        <a href="{{ route('events.create') }}"
                            class="text-white bg-orange-500 hover:bg-orange-400 px-6 py-3 rounded-lg shadow-lg text-lg font-bold">
                            イベントを作成
                        </a>
                    @endif
                </nav>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($events as $event)
                    <div class="bg-white shadow-md rounded-lg p-4">
                        <h2 class="text-2xl font-bold mb-2">{{ $event->title }}</h2>
                        @if($event->image_path)
                        <img src="{{ $event->getImageUrl() }}" alt="イベント画像" class="mb-4 rounded-lg">
                    @endif
                    <p class="text-xl font-bold mb-2">開催日: {{ $event->getFormattedDisplayEventDate() }}</p>
                    <p class="text-lg font-bold mb-2">時間: {{ $event->getFormattedTime() }}</p>
                    <p class="event-info">内容: {{ $event->content }}</p>
                    <p class="mb-1">作成者: {{ $event->user->name }}</p>
                    <p class="mb-4">参加費: 無料</p>

                @if(Auth::user()->membership_id == 1)
                    <p class="text-red-500 font-bold mb-4">
                        参加には <a href="{{ route('profile.edit') }}" class="text-blue-500 underline">POTZ会員</a> の登録をしてください。
                    </p>
                @else
                    @if($event->isOngoing())
                        <a href="{{ $event->zoom_url }}" class="bg-orange-500 text-white font-bold py-2 px-4 rounded hover:bg-orange-700">参加</a>
                    @elseif($event->recurring && !$event->holiday)
                        <p>次回開催日: {{ $event->getNextEventDate() }}</p>
                        <button class="bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-700">準備中</button>
                    @elseif($event->isUpcoming())
                        <button class="bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-700">準備中</button>
                    @else
                        <button class="bg-gray-500 text-white font-bold py-2 px-4 rounded" disabled>終了しました</button>
                    @endif
                @endif

                @if(Auth::user()->membership_id == 5)
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
