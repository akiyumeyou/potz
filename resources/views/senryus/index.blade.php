<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('シルバー川柳') }}
            </h2>
            <a href="{{ route('dashboard') }}"
               class="px-4 py-2 bg-blue-900 text-white text-sm font-bold rounded shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-gray-300">
                戻る
            </a>
        </div>
    </x-slot>
        <style>
        .senryu-text {
            writing-mode: vertical-rl;
            text-orientation: upright;
            font-size: 26px;
            margin-bottom: 1px;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: flex-start;
            height: 280px;
            overflow: hidden;
            padding-left: 10px;
        }
        .senryu-text p {
            margin: 0;
            margin-bottom: 3px;
        }
        .senryu-media {
            width: 100%;
            height: auto;
            object-fit: cover; /* 画像の比率を維持して拡縮 */
            max-height: 2o0px;
            object-fit: contain;
            margin-top: 2px;
            display: block;
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
            height: 550px;
        }
        .preview {
            max-width: 100%;
            max-height: 240px; /* プレビュー画像の最大高さを制限 */
            display: block;
            margin: 10px auto;
        }

        @media (max-width: 768px) {
            .senryu-text, .iine {
                font-size: 24px; /* モバイル用フォントサイズ */
                line-height: 1.6; /* モバイル用行間 */
            }
            .fieldset {
                max-width: 100%; /* コンテンツの最大幅を制限 */
                margin: auto; /* 中央寄せ */
            }
            #drop-area {
                height: 200px;
            }
}
            /* .sort-buttons {
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
                cursor: pointer; */
            /* } */
        </style>
    <body class="bg-yellow-100 flex flex-col items-center justify-center min-h-screen py-20">
        <header class="mb-10 px-2">
            <nav class="mt-4 flex justify-between items-center space-x-2">


                <div class="sort-buttons flex space-x-2">
                    <button id="sortNewest"
                            class="px-6 py-3 text-white bg-green-800 hover:bg-green-400 rounded-lg shadow text-lg font-bold flex items-center justify-center">
                        新着順
                    </button>
                    <button id="sortLikes"
                            class="px-6 py-3 text-white bg-green-800 hover:bg-green-400 rounded-lg shadow text-lg font-bold flex items-center justify-center">
                        いいね順
                    </button>
                </div>
                @if (Auth::check() && Auth::user()->membership_id >= 2)
                <a href="{{ route('senryus.create') }}"
                    class="text-white bg-orange-500 hover:bg-orange-400 px-6 py-3 rounded-lg shadow-lg text-lg font-bold flex items-center justify-center">
                    投稿する
                </a>
            @endif
            </nav>
        </header>

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
                    @if (Auth::id() === $senryu->user_id || (Auth::check() && Auth::user()->membership_id === 5))
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
        <!-- ページネーションリンク -->
        <div class="mt-6 flex justify-center">
            {{ $senryus->links() }}
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
