<x-app-layout>
    <!DOCTYPE html>
    <html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Youtube一覧</title>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <style>
        .sort-text {
            text-decoration: underline;
            cursor: pointer;
        }
        #displayName {
            margin-left: auto;
            padding: 10px;
        }
        #aside {
            width: 22%;
        }
        #content {
            flex-grow: 1;
            padding: 20px;
            box-sizing: border-box;
        }
        #flex {
            display: flex;
            flex-direction: column;
        }
        #output {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 20px;
        }
        .video-container {
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }
        .video-info {
            padding: 10px;
        }
        iframe {
            width: 100%;
            height: 315px; /* 標準の16:9の縦横比 */
        }
        button#send {
            background-color: green;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        div#output p {
            background-color: white;
            padding: 10px;
            margin-bottom: 10px;
        }
        /* .hidden {
            display: none;
        } */
        @media (min-width: 768px) {
            .video-container {
                width: 48%; /* 画面が狭くなった時の横2列 */
            }
        }
        @media (min-width: 1024px) {
            .video-container {
                width: 30%; /* 画面が広い時の横3列 */
            }
        }
        body {
            padding-bottom: 70px; /* フッターの高さを考慮してボディの下部の位置を調整 */
        }
    </style>
    </head>
    <body class="bg-orange-100">

    <header class="text-green-800 p-4 flex items-center justify-between">
        <button id="postButton" class="bg-green-800 text-white p-2 m-2 rounded">新規投稿</button>
        <select id="sortOptions" class="bg-green-800 text-white p-2 m-2 rounded mx-auto">
            <option value="newest">新着順</option>
            <option value="likes">いいね順</option>
            <option value="senior">シニア会員用</option>
            <option value="support">サポート会員用</option>
            <option value="series">シリーズ</option>
        </select>
    </header>

    <main class="flex flex-wrap justify-center p-4">
        <div id="postForm" class="w-full max-w-lg mb-4 hidden">
            <h2>投稿フォーム</h2>
            <form action="{{ route('youtube.store') }}" method="POST">
                @csrf
                <textarea id="text" name="comment" class="w-full p-2 mt-2 mb-4 border" placeholder="動画の説明"></textarea><br>
                <select id="category" name="category" class="w-full p-2 mb-4 border">
                    <option value="support">サポート会員用</option>
                    <option value="senior" selected>シニア会員用</option>
                    <option value="series">シリーズ</option>
                </select><br>
                <input type="text" id="youtubeLink" name="youtube_link" class="w-full p-2 mb-4 border" placeholder="YouTubeリンクをここに貼り付け"><br>
                <button type="submit" id="send" class="bg-green-700 text-white p-2 rounded">送信</button>
            </form>
        </div>

        <div id="output" class="w-full flex flex-wrap justify-between gap-4">
            @foreach ($videos as $video)
            <div class="video-container bg-white shadow rounded p-4" id="message-{{ $video->id }}" data-date="{{ $video->created_at }}" data-likes="{{ $video->like_count }}" data-category="{{ $video->category }}">
                <iframe data-youtube="{{ $video->youtube_link }}" frameborder="0" allowfullscreen class="w-full h-48"></iframe>
                <div class="video-info p-2">
                    <p>{{ $video->comment }}</p>
                    <div class="btn-group flex justify-between mt-2">
                        <form action="{{ route('youtube.updateLikes', $video->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="like-btn bg-white-100 text-green-900 p-2 rounded">❤️ {{ $video->like_count }}</button>
                        </form>
                        <form action="{{ route('youtube.destroy', $video->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="delete-btn bg-red-500 text-white p-2 rounded">削除</button>
                        </form>
                    </div>
                    <div class="mt-2 text-gray-500">
                        <span>{{ $video->user_name }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </main>


    <script>
        function extractVideoID(url) {
            const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
            const match = url.match(regExp);
            return (match && match[2].length === 11) ? match[2] : null;
        }

        function sortAndFilterVideos() {
            const sortOption = document.getElementById('sortOptions').value;
            const videos = Array.from(document.querySelectorAll('.video-container'));

            let sortedVideos = videos;

            if (sortOption === 'newest') {
                sortedVideos = videos.sort((a, b) => {
                    return new Date(b.dataset.date) - new Date(a.dataset.date);
                });
            } else if (sortOption === 'likes') {
                sortedVideos = videos.sort((a, b) => {
                    return parseInt(b.dataset.likes) - parseInt(a.dataset.likes);
                });
            } else {
                sortedVideos = videos.filter(video => video.dataset.category === sortOption);
            }

            const output = document.getElementById('output');
            output.innerHTML = '';
            sortedVideos.forEach(video => {
                output.appendChild(video);
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const iframes = document.querySelectorAll('iframe[data-youtube]');
            iframes.forEach(iframe => {
                const videoID = extractVideoID(iframe.dataset.youtube);
                if (videoID) {
                    iframe.src = `https://www.youtube.com/embed/${videoID}`;
                }
            });

            const sortOptions = document.getElementById('sortOptions');
            sortOptions.addEventListener('change', sortAndFilterVideos);

            // プルダウンフォームのトグル
            const postButton = document.getElementById('postButton');
            const postForm = document.getElementById('postForm');
            postButton.addEventListener('click', function() {
                postForm.classList.toggle('hidden');
            });

            // 初期ソートとフィルタリング
            sortAndFilterVideos();
        });
    </script>
    </body>
    </html>
</x-app-layout>
