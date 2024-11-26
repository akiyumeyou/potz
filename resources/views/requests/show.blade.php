<x-app-layout>
    <x-slot name="header">
        <h2>チャットルーム</h2>
    </x-slot>

    <!-- 成功メッセージ -->
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- チャット履歴 -->
    <div class="chat-container bg-white shadow-xl sm:rounded-lg p-6" style="height: 60%; overflow-y: auto; background-color: #f9f5e7;">
        <ul id="message-list" class="space-y-4">
            @foreach ($meetRoom->meets as $chat)
                <li class="chat-message-container {{ Auth::id() == $chat->sender_id ? 'user' : 'other' }}">
                    @if (Auth::id() != $chat->sender_id)
                        <span class="chat-username text-sm text-gray-500">{{ $chat->sender->name }}</span>
                    @endif
                    <div class="p-2 border rounded-lg chat-message {{ Auth::id() == $chat->sender_id ? 'user' : 'other' }}">
                        <p>{{ $chat->message }}</p>
                    </div>
                    <div class="chat-timestamp text-xs text-gray-400">
                        {{ $chat->created_at->format('Y/m/d H:i') }}
                    </div>
                </li>
            @endforeach
        </ul>
    </div>

    <!-- メッセージ送信フォーム -->
    <form id="chat-form" method="POST" action="{{ route('meet_rooms.store', $meetRoom->id) }}" class="input-area mt-4">
        @csrf
        <div class="flex items-center space-x-2">
            <textarea name="message" id="message" class="form-input flex-1 border-gray-300 rounded-lg" placeholder="メッセージを入力..." required></textarea>
            <button type="submit" class="btn-send">
                <img src="{{ asset('img/btn_send.png') }}" alt="Send" class="w-10 h-10">
            </button>
        </div>
    </form>
</x-app-layout>

