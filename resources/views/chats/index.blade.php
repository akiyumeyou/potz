<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('デジとも広場') }}
            </h2>
            <a href="{{ route('dashboard') }}"
               class="px-4 py-2 bg-blue-900 text-white text-sm font-bold rounded shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-gray-300">
                戻る
            </a>
        </div>
    </x-slot>
    <body class="bg-orange-200 flex justify-center items-center">
        <div class="w-full max-w-md bg-white rounded-lg shadow-lg p-4 flex flex-col mx-auto h-[80vh]">
            <!-- チャットメッセージ表示エリア -->
            <div id="chat-container" class="flex flex-col space-y-4 overflow-y-auto flex-grow p-2">
                <!-- @foreach ($chats as $chat)
                <div class="message flex flex-col
                {{ $chat->user_id == 2 ? 'items-start' : (Auth::id() == $chat->user_id ? 'items-end' : 'items-start') }}">
                    <p class="mb-1 text-sm text-gray-500">
                        {{ $chat->user->name ?? '不明なユーザー' }} -
                        {{ \Carbon\Carbon::parse($chat->created_at)->format('Y-m-d H:i') }}
                    </p>

                    @if ($chat->message_type === 'image' && $chat->content)
                        <a href="{{ asset($chat->content) }}" target="_blank">
                            <img src="{{ asset($chat->content) }}" class="w-32 h-32 rounded-lg">
                        </a>
                    @elseif (!empty($chat->content))
                        <p class="p-3 rounded-lg text-lg {{ Auth::id() == $chat->user_id ? 'bg-green-500 text-white' : 'bg-white border border-gray-300' }}">
                            {{ $chat->content }}
                        </p>
                    @endif
                </div>
                @endforeach -->
            </div>

            <!-- メッセージ入力欄 -->
            <div class="w-full bg-white p-4 shadow-lg flex items-center space-x-2">
                <input id="message-input" type="text" class="flex-1 p-2 border border-gray-300 rounded-lg text-lg" placeholder="メッセージを入力...">
                <input type="file" id="image-input" multiple class="hidden">
                <label for="image-input" class="cursor-pointer bg-green-200 p-2 rounded-lg">🖼️
                </label>
                <!-- 画像プレビューエリア -->
                <div id="image-preview-container" class="flex space-x-2 mt-2"></div>
                <button id="send-button" class="px-4 py-2 bg-blue-500 text-white rounded-lg text-lg">送信</button>
                <button id="ai-button" class="px-4 py-2 bg-orange-500 text-white rounded-lg text-lg"> AIが返事</button>

            </div>
        </div>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const loggedInUserId = @json(Auth::id());
            const chatContainer = document.getElementById("chat-container");
            const messageInput = document.getElementById("message-input");
            const imageInput = document.getElementById("image-input");
            const sendButton = document.getElementById("send-button");
            const previewContainer = document.getElementById("image-preview-container");

            let isUserScrolling = false;

            // **スクロールを一番下にする（ユーザーがスクロール中は実行しない）**
            function scrollToBottom(force = false) {
                if (!isUserScrolling || force) {
                    setTimeout(() => {
                        chatContainer.scrollTop = chatContainer.scrollHeight;
                    }, 100);
                }
            }

            // **スクロールイベントを監視し、スクロール中は更新を止める**
            chatContainer.addEventListener("scroll", function () {
                const atBottom = chatContainer.scrollHeight - chatContainer.scrollTop === chatContainer.clientHeight;
                isUserScrolling = !atBottom;
            });

            // **チャットを取得し、重複追加を防ぐ**
            function fetchChats() {
                if (isUserScrolling) return;

                fetch("{{ route('chats.json') }}")
                    .then(response => response.json())
                    .then(chats => {
                        console.log("fetchChats() 実行: 取得したチャット数 =", chats.length);

                        let existingMessages = new Set();
                        document.querySelectorAll("[data-chat-id]").forEach(msg => {
                            existingMessages.add(msg.getAttribute("data-chat-id"));
                        });

                        chats.forEach(chat => {
                            if (!existingMessages.has(chat.id.toString())) {
                                console.log("appendMessage() 呼び出し (chat.id):", chat.id);
                                appendMessage(chat);
                            } else {
                                console.log(`スキップ: すでに表示済み (chat.id: ${chat.id})`);
                            }
                        });

                        scrollToBottom();
                    })
                    .catch(error => console.error("データ取得エラー:", error));
            }

            setInterval(fetchChats, 5000);
            window.fetchChats = fetchChats;

            // **YouTubeの動画IDを取得**
            function extractYouTubeId(url) {
                let match = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&]+)/);
                return match ? match[1] : null;
            }

            // **メッセージのフォーマット（URLリンク処理 & YouTubeプレビュー対応）**
            function formatMessageContent(content) {
                if (!content) return "";

                let urlRegex = /(https?:\/\/[^\s]+)/g;
                let parts = content.split(urlRegex);
                let formattedContent = "";

                parts.forEach(part => {
                    if (urlRegex.test(part)) {
                        let link = `<a href="${part}" target="_blank" class="text-blue-500 underline">${part}</a>`;

                        if (part.includes("youtube.com/watch") || part.includes("youtu.be")) {
                            let videoId = extractYouTubeId(part);
                            if (videoId) {
                                let youtubePreview = `
                                    <div class="border border-gray-300 rounded-lg p-2 bg-gray-100 mt-2">
                                        <a href="https://www.youtube.com/watch?v=${videoId}" target="_blank">
                                            <img src="https://img.youtube.com/vi/${videoId}/0.jpg" class="w-full rounded-lg">
                                        </a>
                                    </div>`;
                                formattedContent += youtubePreview;
                                return;
                            }
                        }
                        formattedContent += link;
                    } else {
                        formattedContent += part;
                    }
                });

                return formattedContent;
            }

            // **メッセージを追加**
            function appendMessage(chat) {
                if (!chat) {
                    console.error("chat オブジェクトが undefined です");
                    return;
                }

                console.log("appendMessage() 実行 (chat.id):", chat.id);

                if (document.querySelector(`[data-chat-id="${chat.id}"]`)) {
                    console.log(`スキップ: すでに表示済み (chat.id: ${chat.id})`);
                    return;
                }

                let messageDiv = document.createElement("div");
                messageDiv.dataset.chatId = chat.id;
                messageDiv.classList.add("message", "flex", "flex-col", "mb-2");

                if (chat.user_id === loggedInUserId) {
                    messageDiv.classList.add("items-end");
                } else {
                    messageDiv.classList.add("items-start");
                }

                let userInfo = document.createElement("p");
                userInfo.classList.add("mb-1", "text-sm", "text-gray-500");
                userInfo.innerText = `${chat.user_name || "不明なユーザー"} - ${chat.created_at}`;

                let messageContent = document.createElement("p");
                messageContent.classList.add("p-3", "rounded-lg", "text-lg", "max-w-[75%]");

                if (chat.user_id === loggedInUserId) {
                    messageContent.classList.add("bg-green-500", "text-white");
                } else {
                    messageContent.classList.add("bg-white", "border", "border-gray-300");
                }

                // **画像メッセージの処理**
                if (chat.message_type === "image" && chat.content) {
                    let img = document.createElement("img");
                    let basePath = window.location.origin;

                    let imageUrl = "";
                    if (chat.content.startsWith("storage/")) {
                        imageUrl = basePath + "/" + chat.content;
                    } else if (chat.content.startsWith("uploads/")) {
                        imageUrl = basePath + "/storage/" + chat.content;
                    } else {
                        console.error("画像URLの形式が不明:", chat.content);
                        return;
                    }

                    console.log(`画像URL: ${imageUrl}`);
                    img.src = imageUrl;
                    img.classList.add("w-32", "h-auto", "rounded-lg");
                    messageContent.innerHTML = "";
                    messageContent.appendChild(img);
                } else {
                    messageContent.innerHTML = formatMessageContent(chat.content || "");
                }

                messageDiv.appendChild(userInfo);
                messageDiv.appendChild(messageContent);
                chatContainer.appendChild(messageDiv);
            }

            // **画像プレビュー機能**
            imageInput.addEventListener("change", function () {
            previewContainer.innerHTML = "";
            const aiButton = document.getElementById("ai-button"); // ✅ 修正: `#ai-button` を取得

            if (imageInput.files.length > 0) {
                let file = imageInput.files[0];
                let reader = new FileReader();
                reader.onload = function (e) {
                    let img = document.createElement("img");
                    img.src = e.target.result;
                    img.classList.add("w-16", "h-16", "rounded-lg", "border");
                    previewContainer.appendChild(img);
                };
                reader.readAsDataURL(file);

                // ✅ 画像を選択したら「AIが返事」ボタンを非表示
                if (aiButton) aiButton.style.display = "none";
            } else {
                // ✅ 画像を削除したら「AIが返事」ボタンを再表示
                if (aiButton) aiButton.style.display = "block";
            }
            });


            // **メッセージ送信**
            sendButton.addEventListener("click", function () {
                let formData = new FormData();
                formData.append("content", messageInput.value.trim());
                formData.append("_token", "{{ csrf_token() }}");

                if (imageInput.files.length > 0) {
                    formData.append("image", imageInput.files[0]);
                }

                fetch("{{ route('chats.store') }}", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(chat => {
                    appendMessage(chat);
                    messageInput.value = "";
                    imageInput.value = "";
                    previewContainer.innerHTML = "";
                    // ✅ AIボタンを再表示する
                    const aiButton = document.getElementById("ai-button");
                    if (aiButton) aiButton.style.display = "block";

                    scrollToBottom(true);
                })
                .catch(error => console.error("送信エラー:", error));
            });

            fetchChats();
            scrollToBottom(true);
        });
        </script>
@vite(['resources/js/chat_ai.js'])

    </body>
</x-app-layout>

