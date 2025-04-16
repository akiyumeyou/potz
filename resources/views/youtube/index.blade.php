<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('みんなの動画') }}
            </h2>
            <a href="{{ route('dashboard') }}"
                class="px-4 py-2 bg-blue-900 text-white text-sm font-bold rounded shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-gray-300">
                戻る
            </a>
        </div>
    </x-slot>
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
        <div class="max-w-7xl mx-auto px-4 py-6">
            <!-- タブ形式のカテゴリ選択 -->
            <div class="mb-6">
                <div class="flex flex-wrap gap-2 text-lg">
                    <button
                        class="category-tab px-6 py-3 rounded-t-lg bg-white text-blue-800 font-bold border-b-4 border-blue-800"
                        data-category="exercise">👴 健康</button>
                    <button class="category-tab px-6 py-3 rounded-t-lg bg-gray-100 text-gray-600"
                        data-category="life">💰 暮らし</button>
                    <button class="category-tab px-6 py-3 rounded-t-lg bg-gray-100 text-gray-600"
                        data-category="cooking">🍳 料理</button>
                    <button class="category-tab px-6 py-3 rounded-t-lg bg-gray-100 text-gray-600"
                        data-category="digital">📱 デジタル</button>
                    <button class="category-tab px-6 py-3 rounded-t-lg bg-gray-100 text-gray-600"
                        data-category="hobby">🎨 趣味</button>
                    <button class="category-tab px-6 py-3 rounded-t-lg bg-gray-100 text-gray-600"
                        data-category="animals">🐾 動物</button>
                    <button class="category-tab px-6 py-3 rounded-t-lg bg-gray-100 text-gray-600" data-category="ai">🤖
                        AI情報</button>
                    <button class="category-tab px-6 py-3 rounded-t-lg bg-gray-100 text-gray-600"
                        data-category="user">👥 みんなの動画</button>
                </div>
            </div>

            <!-- 検索フォーム -->
            <div class="mb-6">
                <div class="flex gap-2">
                    <input type="text" id="searchInput" class="flex-1 p-3 text-lg border rounded-lg"
                        placeholder="動画を検索">
                    <button id="searchButton"
                        class="bg-blue-800 text-white px-6 py-3 rounded-lg text-lg hover:bg-blue-700">🔍 検索</button>
                </div>
            </div>

            <!-- 投稿フォーム -->
            <div id="postForm" class="max-w-lg mb-6 hidden mx-auto bg-white p-6 rounded-lg shadow">
                <h2 class="text-xl font-bold mb-4">📝 新規投稿</h2>
                <form action="{{ route('youtube.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-700 text-lg mb-2">動画の説明</label>
                        <textarea id="text" name="comment" class="w-full p-3 text-lg border rounded-lg"
                            placeholder="動画の説明を入力"></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-lg mb-2">カテゴリ</label>
                        <select id="category" name="category" class="w-full p-3 text-lg border rounded-lg">
                            <option value="support">サポート会員用</option>
                            <option value="senior" selected>シニア会員用</option>
                            <option value="series">シリーズ</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-lg mb-2">YouTubeリンク</label>
                        <input type="text" id="youtubeLink" name="youtube_link"
                            class="w-full p-3 text-lg border rounded-lg" placeholder="YouTubeリンクを貼り付け">
                    </div>
                    <button type="submit"
                        class="bg-green-700 text-white px-6 py-3 rounded-lg text-lg hover:bg-green-600 w-full">投稿する</button>
                </form>
            </div>

            <!-- ユーザー投稿の表示コントロール -->
            <div id="userVideosControls" class="mb-6 hidden">
                <div class="flex gap-4">
                    @if (Auth::check() && Auth::user()->membership_id >= 3)
                    <button id="postButton"
                        class="bg-orange-600 text-white px-6 py-3 rounded-lg text-lg hover:bg-orange-500">📝
                        新規投稿</button>
                    @endif
                    <select id="sortOptions" class="bg-blue-800 text-white px-6 py-3 rounded-lg text-lg">
                        <option value="newest">新着順</option>
                        <option value="likes">いいね順</option>
                    </select>
                </div>
            </div>

            <!-- 動画一覧 -->
            <div id="output" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($videos as $video)
                <div class="video-container bg-white shadow rounded-lg p-4" id="message-{{ $video->id }}"
                    data-date="{{ $video->created_at }}" data-likes="{{ $video->like_count }}"
                    data-category="{{ $video->category }}">
                    <div class="aspect-w-4 aspect-h-3 mb-4">
                        <iframe data-youtube="{{ $video->youtube_link }}" frameborder="0" allowfullscreen
                            class="w-full h-full rounded-lg"></iframe>
                    </div>
                    <div class="video-info">
                        <h3 class="text-xl font-bold mb-2">{{ $video->comment }}</h3>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-600 text-lg">👁️ {{ $video->view_count ?? '0' }}回視聴</span>
                            <span class="text-gray-600 text-lg">📅 {{ $video->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <form action="{{ route('youtube.updateLikes', $video->id) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="like-btn bg-gray-100 text-red-600 px-4 py-2 rounded-lg text-lg hover:bg-red-100">❤️
                                    {{ $video->like_count }}</button>
                            </form>
                            @if (Auth::check() && (Auth::id() === $video->user_id || Auth::user()->membership_id === 5))
                            <form action="{{ route('youtube.destroy', $video->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="delete-btn bg-red-500 text-white px-4 py-2 rounded-lg text-lg hover:bg-red-600">🗑️
                                    削除</button>
                            </form>
                            @endif
                        </div>
                        <div class="mt-2 text-gray-600 text-lg">
                            <span>👤 {{ $video->user_name }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- ページネーション -->
            <div class="mt-8">
                {{ $videos->links() }}
            </div>
        </div>

        <script>
            const authData = {!! json_encode([
                'userId' => Auth::id(),
                'isModerator' => Auth::check() && optional(Auth::user())->membership_id === 5,
            ]) !!};
        

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

            // カテゴリごとの検索キーワード
            const categoryKeywords = {
                exercise: '家で運動 いきいき体操 ヨガ',
                life: '年金 高齢者向けサービス',
                cooking: 'シニア向け料理 おすすめレシピ',
                digital: 'スマホ 使い方 パソコン 初心者 高齢者',
                hobby: '盆栽 カメラ',
                animals: 'ペット 動物 癒し かわいい',
                ai: '生成AI ChatGPT 最新'
            };

            // タブの切り替え機能
            document.querySelectorAll('.category-tab').forEach(tab => {
                tab.addEventListener('click', function () {
                    // すべてのタブを非アクティブに
                    document.querySelectorAll('.category-tab').forEach(t => {
                        t.classList.remove('bg-white', 'text-blue-800', 'border-b-4', 'border-blue-800');
                        t.classList.add('bg-gray-100', 'text-gray-600');
                    });

                    // クリックされたタブをアクティブに
                    this.classList.remove('bg-gray-100', 'text-gray-600');
                    this.classList.add('bg-white', 'text-blue-800', 'border-b-4', 'border-blue-800');

                    const category = this.dataset.category;
                    if (category === 'user') {
                        // ユーザー投稿を表示
                        document.getElementById('userVideosControls').classList.remove('hidden');
                        document.getElementById('postForm').classList.add('hidden');
                        showUserVideos();
                    } else {
                        // YouTube検索を実行
                        document.getElementById('userVideosControls').classList.add('hidden');
                        const query = categoryKeywords[category];
                        document.getElementById('searchInput').value = query;
                        searchYouTube(query);
                    }
                });
            });

            // ユーザー投稿の表示
            function showUserVideos() {
                const output = document.getElementById('output');
                output.innerHTML = '';

                // CSRFトークンの取得
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

                // 既存のユーザー投稿を表示
                const videos = @json($videos);
                if (videos && videos.data) {
                    // 新着順にソート
                    const sortedVideos = videos.data.sort((a, b) => {
                        return new Date(b.created_at) - new Date(a.created_at);
                    });

                    sortedVideos.forEach(video => {
                        const videoContainer = document.createElement('div');
                        videoContainer.className = 'video-container bg-white shadow rounded-lg p-4';
                        videoContainer.setAttribute('data-date', video.created_at);
                        videoContainer.setAttribute('data-likes', video.like_count);
                        videoContainer.setAttribute('data-category', video.category);

                        const videoId = extractVideoID(video.youtube_link);
                        const likeForm = `
                                            <form action="/youtubes/${video.id}/likes" method="POST" class="inline">
                                                <input type="hidden" name="_token" value="${csrfToken}">
                                                <button type="submit" class="text-lg">❤️ ${video.like_count}</button>
                                            </form>
                                        `;

                        const deleteForm = authData.isModerator || (authData.userId && authData.userId === video.user_id) ? `
                                            <form action="/youtubes/${video.id}" method="POST" class="inline">
                                                <input type="hidden" name="_token" value="${csrfToken}">
                                                <input type="hidden" name="_method" value="DELETE">
                                                <button type="submit" class="text-red-500 text-lg">🗑️</button>
                                            </form>
                                        ` : '';

                        videoContainer.innerHTML = `
                                            <div class="relative" style="padding-bottom: 75%;">
                                                <iframe 
                                                    src="https://www.youtube.com/embed/${videoId}" 
                                                    frameborder="0" 
                                                    allowfullscreen 
                                                    class="absolute top-0 left-0 w-full h-full rounded-lg"
                                                ></iframe>
                                            </div>
                                            <div class="video-info mt-4">
                                                <h3 class="text-xl font-bold mb-2">${video.comment}</h3>
                                                <div class="flex justify-between items-center mb-2">
                                                    <span class="text-gray-600 text-lg view-count" data-video-id="${videoId}">👁️ 読み込み中...</span>
                                                    <span class="text-gray-600 text-lg">📅 ${new Date(video.created_at).toLocaleDateString('ja-JP')}</span>
                                                </div>
                                                <div class="flex justify-between items-center">
                                                    <span class="text-gray-600 text-lg">👤 ${video.user_name}</span>
                                                    <div class="flex items-center gap-2">
                                                        ${likeForm}
                                                        ${deleteForm}
                                                    </div>
                                                </div>
                                            </div>
                                        `;

                        output.appendChild(videoContainer);

                        // 視聴回数を取得
                        fetchVideoStats(videoId);
                    });
                }
            }

            // 投稿ボタンの機能
            document.getElementById('postButton').addEventListener('click', function () {
                document.getElementById('postForm').classList.toggle('hidden');
            });

            // 検索ボタンの機能
            document.getElementById('searchButton').addEventListener('click', function () {
                const query = document.getElementById('searchInput').value;
                if (query.trim() !== '') {
                    searchYouTube(query);
                }
            });

            // 検索入力欄のEnterキーイベント
            document.getElementById('searchInput').addEventListener('keypress', function (e) {
                if (e.key === 'Enter') {
                    const query = this.value;
                    if (query.trim() !== '') {
                        searchYouTube(query);
                    }
                }
            });

            // 並び替え機能
            document.getElementById('sortOptions').addEventListener('change', function () {
                const sortOption = this.value;
                const videos = Array.from(document.querySelectorAll('.video-container'));
                const output = document.getElementById('output');

                videos.sort((a, b) => {
                    if (sortOption === 'newest') {
                        return new Date(b.dataset.date) - new Date(a.dataset.date);
                    } else if (sortOption === 'likes') {
                        return parseInt(b.dataset.likes) - parseInt(a.dataset.likes);
                    }
                    return 0;
                });

                output.innerHTML = '';
                videos.forEach(video => output.appendChild(video));
            });

            // 初期表示時はユーザー投稿を新着順で表示
            document.addEventListener('DOMContentLoaded', function () {
                showUserVideos();
            });

            // YouTube検索機能
            async function searchYouTube(query) {
                const output = document.getElementById('output');
                output.innerHTML = '<div class="text-center p-4 text-xl">🔍 検索中...</div>';

                try {
                    const response = await fetch(`/youtubes/search?query=${encodeURIComponent(query)}&order=date`);
                    const data = await response.json();

                    if (data.error) {
                        output.innerHTML = `<div class="text-red-500 p-4 text-xl">⚠️ ${data.error}</div>`;
                        return;
                    }

                    if (!data.items || data.items.length === 0) {
                        output.innerHTML = '<div class="text-center p-4 text-xl">🔍 検索結果が見つかりませんでした</div>';
                        return;
                    }

                    output.innerHTML = '';

                    // 日付順に並び替え（新しい順）
                    const sortedItems = data.items.sort((a, b) => {
                        return new Date(b.snippet.publishedAt) - new Date(a.snippet.publishedAt);
                    });

                    sortedItems.forEach(item => {
                        const videoId = item.id.videoId;
                        const title = item.snippet.title;
                        const publishedAt = new Date(item.snippet.publishedAt);
                        const now = new Date();
                        const diffTime = Math.abs(now - publishedAt);
                        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                        const videoElement = document.createElement('div');
                        videoElement.className = 'video-container bg-white shadow rounded-lg p-4';
                        videoElement.innerHTML = `
                            <div class="relative" style="padding-bottom: 75%;">
                                <iframe 
                                    src="https://www.youtube.com/embed/${videoId}" 
                                    frameborder="0" 
                                    allowfullscreen 
                                    class="absolute top-0 left-0 w-full h-full rounded-lg"
                                ></iframe>
                            </div>
                            <div class="video-info mt-4">
                                <h3 class="text-xl font-bold mb-2">${title}</h3>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-gray-600 text-lg view-count" data-video-id="${videoId}">👁️ 読み込み中...</span>
                                    <span class="text-gray-600 text-lg">📅 ${diffDays}日前</span>
                                </div>
                            </div>
                        `;

                        output.appendChild(videoElement);

                        // 視聴回数を取得
                        fetchVideoStats(videoId);
                    });
                } catch (error) {
                    console.error('Error searching YouTube:', error);
                    output.innerHTML = `
                        <div class="text-red-500 p-4 text-xl">
                            ⚠️ 検索中にエラーが発生しました
                        </div>
                    `;
                }
            }

            // 視聴回数を取得する関数
            async function fetchVideoStats(videoId) {
                try {
                    const response = await fetch(`/youtubes/stats/${videoId}`);
                    const data = await response.json();

                    if (data.statistics) {
                        const viewCount = parseInt(data.statistics.viewCount);
                        const formattedCount = viewCount >= 10000
                            ? `${Math.floor(viewCount / 10000)}万回視聴`
                            : `${viewCount}回視聴`;

                        const element = document.querySelector(`.view-count[data-video-id="${videoId}"]`);
                        if (element) {
                            element.textContent = `👁️ ${formattedCount}`;
                        }
                    }
                } catch (error) {
                    console.error('Error fetching video stats:', error);
                }
            }
        </script>
    </body>

    </html>
</x-app-layout>