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
<div h-auto>
<div class="flex justify-center items-center h-auto">
    @if ($userRequest->status_id === 2)
        <!-- 確定ボタン -->
<form action="{{ route('matchings.confirm') }}" method="POST" class="inline-block">
    @csrf
    <input type="hidden" name="request_id" value="{{ $userRequest->id }}">
    <input type="hidden" name="supporter_id" value="{{ $userRequest->supporter_id }}">
    <input type="hidden" name="confirmed_by" value="{{ Auth::id() }}">
    <button type="submit"
        class="w-full sm:w-auto px-8 py-3 bg-blue-500 text-white text-xl font-bold rounded-lg shadow-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300">
        確定する
    </button>
</form>
    @elseif ($userRequest->status_id === 3)
        <!-- 成立中表示 -->
        <span class="w-full sm:w-auto px-8 py-3 bg-orange-300 text-white text-xl font-bold rounded-lg shadow-md text-center">
            成立中
        </span>

         <!-- 領収書発行ボタン: サポートさんのみ -->
         @if (Auth::id() === $userRequest->supporter_id && Auth::user()->membership_id === 3)
         <a href="{{ route('receipts.show', ['request_id' => $userRequest->id]) }}"
            class="w-full sm:w-auto px-8 py-3 bg-blue-500 text-white text-xl font-bold rounded-lg shadow-md text-center hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300">
             領収書発行
         </a>
     @endif

@elseif ($userRequest->status_id === 4)
    <!-- 領収書表示ボタン: サポートさんと依頼者双方 -->
    @if (Auth::id() === $userRequest->supporter_id || Auth::id() === $userRequest->requester_id)
        <a href="{{ route('receipts.generatePdf', ['request_id' => $userRequest->id]) }}"
           class="w-full sm:w-auto px-8 py-3 bg-green-500 text-white text-xl font-bold rounded-lg shadow-md text-center hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-300">
            領収書を見る
        </a>
    @endif
@endif
</div>

        <!-- 編集セクション -->
        @if (!in_array($userRequest->status_id, [3, 4]))
            @include('requests.edit')
        @else
            <p class="text-red-500 font-bold">マッチング成立後は編集できません。</p>
        @endif


        </div>

    <!-- チャット履歴 -->
    <div class="chat-container bg-white shadow-xl sm:rounded-lg p-6" style="height: 60%; overflow-y: auto; background-color: #f9f5e7;">
        <ul id="message-list" class="space-y-4">
            @foreach ($meetRoom->meets as $chat)
                <li class="chat-message-container flex {{ Auth::id() == $chat->sender_id ? 'justify-end' : 'justify-start' }}">
                    @if (Auth::id() != $chat->sender_id)
                        <span class="chat-username text-sm text-gray-500 mr-2">{{ $chat->sender->name }}</span>
                    @endif
                    <div class="p-2 border rounded-lg max-w-xs {{ Auth::id() == $chat->sender_id ? 'bg-blue-100 text-right' : 'bg-gray-100 text-left' }}">
                        @if ($chat->image)
                            <img src="{{ asset('storage/' . $chat->image) }}" alt="Uploaded Image" class="mb-2 max-w-full rounded-lg">
                        @endif
                        <!-- <p>{!! nl2br(e($chat->message)) !!}</p> -->
                        <p>{!! nl2br(formatLinks(e($chat->message))) !!}</p>

                    </div>
                    <div class="chat-timestamp text-xs text-gray-400 mt-1">
                        {{ $chat->created_at->format('Y/m/d H:i') }}
                    </div>
                </li>
            @endforeach
        </ul>
    </div>

    <!-- メッセージ送信フォーム -->
    <form id="chat-form" method="POST" action="{{ route('meet_rooms.store', $meetRoom->id) }}" enctype="multipart/form-data" class="input-area mt-4">
        @csrf
        <div class="flex items-center space-x-2">
            <textarea name="message" id="message" class="form-input flex-1 border-gray-300 rounded-lg" placeholder="メッセージを入力..." required></textarea>
            <button type="submit" class="btn-send">
                <img src="{{ asset('img/btn_send.png') }}" alt="Send" class="w-10 h-10">
            </button>
        </div>
    </form>

    <!-- 画像アップロードフォーム -->
    <form id="image-upload-form" method="POST" action="{{ route('meet_rooms.image', $meetRoom->id) }}" enctype="multipart/form-data" class="input-area mt-4">
        @csrf
        <div class="flex items-center space-x-2">
            <input type="file" name="image" id="image-input" accept="image/*" class="form-input border-gray-300 rounded-lg">
            <button type="submit" id="image-submit-button" class="btn-send hidden">
                <img src="{{ asset('img/btn_send.png') }}" alt="Send Image" class="w-10 h-10">
            </button>
        </div>
    </form>
</div>
    <script>
        document.getElementById('image-input').addEventListener('change', function () {
            const submitButton = document.getElementById('image-submit-button');
            if (this.files.length > 0) {
                submitButton.classList.remove('hidden');
            } else {
                submitButton.classList.add('hidden');
            }
        });
    </script>

</x-app-layout>
