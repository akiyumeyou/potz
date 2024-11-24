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
    <div>
        @foreach ($meetRoom->meets as $chat)
            <div>
                <strong>{{ $chat->sender->name }}:</strong>
                <p>{{ $chat->message }}</p>
            </div>
        @endforeach
    </div>

    <!-- メッセージ送信フォーム -->
    <form method="POST" action="{{ route('meet_rooms.store', $meetRoom->id) }}">
        @csrf
        <textarea name="message" required></textarea>
        <button type="submit">送信</button>
    </form>
</x-app-layout>
