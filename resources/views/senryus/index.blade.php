<x-app-layout>
    <!-- <!DOCTYPE html>
    <html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet"> -->
        <style>
            .senryu-text {
                writing-mode: vertical-rl;
                text-orientation: upright;
                font-size: 28px;
                margin-bottom: 1px;
                display: flex;
                flex-direction: column;
                align-items: flex-start;
                justify-content: flex-start;
                height: 240px;
                padding-left: 10px;
            }
            .senryu-text p {
                margin: 0;
                margin-bottom: 3px;
            }
            .senryu-media {
                width: 100%;
                height: auto;
                max-height: 280px;
                object-fit: contain;
                margin-top: 2px;
            }
            .senryu-meta {
                display: flex;
                justify-content: space-between;
                width: 100%;
                padding: 0 10px;
                margin-top: 5px;
            }
            .senryu-item {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: flex-start;
                width: 100%;
                height: 560px;
            }
            .sort-buttons {
                display: flex;
                justify-content: center;
                margin-bottom: 20px;
            }
            .sort-buttons button {
                background-color: green;
                color: white;
                padding: 10px 20px;
                border: none;
                border-radius: 5px;
                margin: 0 10px;
                cursor: pointer;
            }
        </style>
    <body class="bg-yellow-100 flex flex-col items-center justify-center min-h-screen py-20">
        <header class="mb-10">
            <h1 class="text-xl font-bold mb-6">シルバー川柳</h1>
            <nav class="mt-4 flex justify-end pr-10">
                @if (Auth::check() && Auth::user()->membership_id >= 2)
                    <a href="{{ route('senryus.create') }}"
                        class="text-white bg-orange-500 hover:bg-orange-400 px-6 py-2 rounded-lg shadow-lg text-lg font-bold">
                        新規投稿
                    </a>
                @endif
            </nav>
        </header>

        <div class="sort-buttons">
            <button id="sortNewest">新着順</button>
            <button id="sortLikes">いいね順</button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 w-11/12 mx-auto" id="senryuContainer">
            @foreach ($senryus as $senryu)
                <div class="bg-white p-4 rounded-lg shadow-lg senryu-item" data-id="{{ $senryu->id }}" data-date="{{ $senryu->created_at }}" data-likes="{{ $senryu->iine }}">
                    <div class="senryu-text">
                        <p>{{ $senryu->s_text1 }}</p>
                        <p>{{ $senryu->s_text2 }}</p>
                        <p>{{ $senryu->s_text3 }}</p>
                    </div>
                @if ($senryu->img_path)
                    @if (Str::endsWith($senryu->img_path, ['.mp4', '.mov', '.avi']))
                        <video src="{{ $senryu->img_path }}" controls class="preview"></video>
                    @else
                        <img src="{{ $senryu->img_path }}" class="preview">
                    @endif

                @else
                    <img src="{{ asset('storage/senryus/dummy.jpg') }}" class="senryu-media">
                @endif
                    <div class="senryu-meta mt-2">
                        @if (Auth::id() === $senryu->user_id)
                            <a href="{{ route('senryus.edit', $senryu->id) }}" class="text-blue-500 hover:underline">{{ $senryu->user_name }}</a>
                        @else
                            <span>{{ $senryu->user_name }}</span>
                        @endif
                        <form action="{{ route('senryus.incrementIine', ['id' => $senryu->id]) }}" method="POST">
                            @csrf
                            <button type="submit" class="iine-btn bg-white-500 text-green-900 p-2 rounded">❤️ {{ $senryu->iine }}</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- <footer id="footer" class="w-full bg-green-800 text-white text-center p-2 fixed bottom-0">
            <img src="{{ asset('img/logo.png') }}" alt="potz" class="inline-block w-8 h-8">
            <a href="https://potz.jp/" class="text-white underline">https://potz.jp/</a>
        </footer> -->

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const senryuContainer = document.getElementById('senryuContainer');
                const sortNewest = document.getElementById('sortNewest');
                const sortLikes = document.getElementById('sortLikes');

                function sortSenryus(sortBy) {
                    const senryuItems = Array.from(senryuContainer.children);

                    senryuItems.sort((a, b) => {
                        if (sortBy === 'newest') {
                            return new Date(b.dataset.date) - new Date(a.dataset.date);
                        } else if (sortBy === 'likes') {
                            return parseInt(b.dataset.likes) - parseInt(a.dataset.likes);
                        }
                    });

                    senryuContainer.innerHTML = '';
                    senryuItems.forEach(item => senryuContainer.appendChild(item));
                }

                sortNewest.addEventListener('click', () => sortSenryus('newest'));
                sortLikes.addEventListener('click', () => sortSenryus('likes'));

                sortSenryus('newest'); // デフォルトで新着順にソート
            });
        </script>
    </body>
</x-app-layout>
