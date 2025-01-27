<x-app-layout>
    <!DOCTYPE html>
    <html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Youtube一覧</title>
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    </head>
    <body class="bg-orange-100">

    <header class="text-green-800 p-4 flex items-center justify-between">
        @if (Auth::check() && Auth::user()->membership_id >= 3)
            <button id="postButton" class="bg-orange-600 text-white p-2 rounded hover:bg-orange-500">新規投稿</button>
        @endif
        <select id="sortOptions" class="bg-green-800 text-white p-2 rounded">
            <option value="newest">新着順</option>
            <option value="likes">いいね順</option>
            <option value="senior">シニア会員用</option>
            <option value="support">サポート会員用</option>
            <option value="series">シリーズ</option>
        </select>
    </header>

    <main class="p-4">
        <!-- 投稿フォーム -->
        <div id="postForm" class="max-w-lg mb-4 hidden mx-auto">
            <h2 class="text-xl font-bold mb-4">投稿フォーム</h2>
            <form action="{{ route('youtube.store') }}" method="POST">
                @csrf
                <textarea id="text" name="comment" class="w-full p-2 mb-4 border rounded" placeholder="動画の説明"></textarea>
                <select id="category" name="category" class="w-full p-2 mb-4 border rounded">
                    <option value="support">サポート会員用</option>
                    <option value="senior" selected>シニア会員用</option>
                    <option value="series">シリーズ</option>
                </select>
                <input type="text" id="youtubeLink" name="youtube_link" class="w-full p-2 mb-4 border rounded" placeholder="YouTubeリンクをここに貼り付け">
                <button type="submit" id="send" class="bg-green-700 text-white p-2 rounded hover:bg-green-600">送信</button>
            </form>
        </div>

        <!-- 動画一覧 -->
        <div id="output" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($videos as $video)
                <div class="video-container bg-white shadow rounded p-4" id="message-{{ $video->id }}" data-date="{{ $video->created_at }}" data-likes="{{ $video->like_count }}" data-category="{{ $video->category }}">
                    <iframe data-youtube="{{ $video->youtube_link }}" frameborder="0" allowfullscreen class="w-full h-48"></iframe>
                    <div class="video-info mt-2">
                        <p class="text-gray-800">{{ $video->comment }}</p>
                        <div class="flex justify-between items-center mt-2">
                            <form action="{{ route('youtube.updateLikes', $video->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="like-btn bg-gray-100 text-green-900 px-4 py-2 rounded hover:bg-green-200">❤️ {{ $video->like_count }}</button>
                            </form>
                            @if (Auth::check() && (Auth::id() === $video->user_id || Auth::user()->membership_id === 5))
                            <form action="{{ route('youtube.destroy', $video->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="delete-btn bg-red-500 text-white p-2 rounded">削除</button>
                            </form>
                            @endif

                        </div>
                        <div class="mt-2 text-gray-500">
                            <span>{{ $video->user_name }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- ページネーション -->
        <div class="mt-8">
            {{ $videos->links() }}
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

            const postButton = document.getElementById('postButton');
            const postForm = document.getElementById('postForm');
            postButton.addEventListener('click', function() {
                postForm.classList.toggle('hidden');
            });

            sortAndFilterVideos();
        });
    </script>
    </body>
    </html>
</x-app-layout>
