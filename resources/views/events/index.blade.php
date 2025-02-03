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

    <body class="bg-orange-200">
            @if (session('success'))
                <div class="alert alert-success bg-green-100 text-green-800 p-4 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif
            <div class="container mx-auto px-4 py-1">

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

            {{-- 今日のイベント --}}
            @if ($todayEvents->isNotEmpty())
                <h2 class="text-xl font-semibold mb-2 text-red-600">今日のイベント</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($todayEvents as $event)
                        <div class="bg-white shadow-md rounded-lg p-4">
                            <h2 class="text-2xl font-bold mb-2">{{ $event->title }}</h2>
                            @if($event->image_path)
                                <img src="{{ $event->getImageUrl() }}" alt="イベント画像" class="w-full h-56 object-cover rounded-lg">
                            @endif
                            <p class="text-xl font-bold mb-2">開催日: {{ $event->getFormattedDisplayEventDate() }}</p>
                            <p class="text-lg font-bold mb-2">時間: {{ $event->getFormattedTime() }}</p>
                            <p class="event-info">内容: {{ $event->content }}</p>
                            <p class="mb-1">作成者: {{ $event->user->name }}</p>

                            {{-- 料金表示 --}}
                            <p class="mb-4">
                                参加費: {{ $event->is_paid ? '¥' . number_format($event->price, 0) : '無料' }}
                            </p>

                            {{-- 未来のイベントのロジック --}}
                            @if($event->recurring && !$event->holiday)
                                <p class="text-sm text-gray-600">次回開催日: {{ $event->getNextEventDate() }} ({{ $event->getRecurringTypeLabel() }})</p>
                            @endif

                            {{-- 参加ロジック --}}
                            @if(Auth::user()->membership_id == 1)
                                <p class="text-red-500 font-bold mb-4">
                                    参加には <a href="{{ route('profile.edit') }}" class="text-blue-500 underline">POTZ会員</a> の登録をしてください。
                                </p>
                            @else
                                @php
                                    $participant = $event->participants()->where('user_id', Auth::id())->first();
                                @endphp

                                @if (!$event->is_paid)
                                {{-- 無料イベント --}}
                                @if ($event->isOngoing())
                                    <a id="event-button-{{ $event->id }}" href="{{ $event->zoom_url }}"
                                       class="event-button bg-orange-500 text-white font-bold py-2 px-4 rounded text-center w-full hover:bg-orange-700"
                                       data-event-id="{{ $event->id }}">
                                        参加
                                    </a>
                                @elseif ($event->isUpcoming())
                                    <button id="event-button-{{ $event->id }}"
                                            class="event-button bg-blue-500 text-white font-bold py-2 px-4 rounded text-center w-full opacity-50 cursor-not-allowed"
                                            data-event-id="{{ $event->id }}">
                                        参加
                                    </button>
                                @else
                                    <div id="event-button-{{ $event->id }}"
                                         class="event-button bg-gray-300 text-gray-600 font-bold py-2 px-4 rounded text-center w-full"
                                         data-event-id="{{ $event->id }}">
                                        終了しました
                                    </div>
                                @endif
                            @else
                                {{-- 有料イベント --}}
                                @if ($participant)
                                    @if ($participant->status == 0)
                                        <p class="text-gray-600 font-bold mt-2">主催者の確認をお待ちください。</p>
                                    @elseif ($participant->status == 1)
                                        @if ($event->isOngoing())
                                            <a id="event-button-{{ $event->id }}" href="{{ $event->zoom_url }}"
                                               class="event-button bg-orange-500 text-white font-bold py-2 px-8 rounded text-center w-full hover:bg-orange-700"
                                               data-event-id="{{ $event->id }}">
                                                参加
                                            </a>
                                        @elseif ($event->isUpcoming())
                                            <button id="event-button-{{ $event->id }}"
                                                    class="event-button bg-green-600 text-white font-bold py-2 px-4 rounded text-center w-full opacity-50 cursor-not-allowed"
                                                    data-event-id="{{ $event->id }}">
                                                予約済み
                                            </button>
                                        @else
                                            <div id="event-button-{{ $event->id }}"
                                                 class="event-button bg-gray-300 text-gray-600 font-bold py-2 px-4 rounded text-center w-full"
                                                 data-event-id="{{ $event->id }}">
                                                終了しました
                                            </div>
                                        @endif
                                    @endif
                                @else
                                    @if ($event->isUpcoming())
                                        <button id="event-button-{{ $event->id }}" onclick="openPaymentModal({{ $event->id }})"
                                                class="event-button bg-blue-500 text-white font-bold py-2 px-4 rounded text-center w-full hover:bg-blue-700"
                                                data-event-id="{{ $event->id }}">
                                            参加予約
                                        </button>
                                    @elseif ($event->isOngoing())
                                        <a id="event-button-{{ $event->id }}" href="{{ $event->zoom_url }}"
                                           class="event-button bg-orange-500 text-white font-bold py-2 px-8 rounded text-center w-full hover:bg-orange-700"
                                           data-event-id="{{ $event->id }}">
                                            参加
                                        </a>
                                    @else
                                        <div id="event-button-{{ $event->id }}"
                                             class="event-button bg-gray-300 text-gray-600 font-bold py-2 px-4 rounded text-center w-full"
                                             data-event-id="{{ $event->id }}">
                                            終了しました
                                        </div>
                                    @endif
                                @endif
                            @endif
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            @if ($todayEvents->isEmpty())
                <p class="col-span-full text-center">今日のイベントはありません。</p>
            @endif
            </div>
    </div>
    <div class="container mx-auto px-4 py-1">

    {{-- 未来のイベント --}}
    @if ($futureEvents->isNotEmpty())
        <h2 class="text-xl font-semibold mt-6 mb-2">今後のイベント</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($futureEvents as $event)
                <div class="bg-white shadow-md rounded-lg p-4">
                    <h2 class="text-2xl font-bold mb-2">{{ $event->title }}</h2>
                    @if($event->image_path)
                        <img src="{{ $event->getImageUrl() }}" alt="イベント画像" class="w-full h-56 object-cover rounded-lg">
                    @endif
                    <p class="text-xl font-bold mb-2">開催日: {{ $event->getFormattedDisplayEventDate() }}</p>
                    <p class="text-lg font-bold mb-2">時間: {{ $event->getFormattedTime() }}</p>
                    <p class="event-info">内容: {{ $event->content }}</p>
                    <p class="mb-1">作成者: {{ $event->user->name }}</p>

                    {{-- 料金表示 --}}
                    <p class="mb-4">
                        参加費: {{ $event->is_paid ? '¥' . number_format($event->price, 0) : '無料' }}
                    </p>

                    {{-- 未来のイベントのロジック --}}
                    @if($event->recurring && !$event->holiday)
                        <p class="text-sm text-gray-600">次回開催日: {{ $event->getNextEventDate() }} ({{ $event->getRecurringTypeLabel() }})</p>
                    @endif

                    {{-- 参加ロジック --}}
                    @if(Auth::user()->membership_id == 1)
                        <p class="text-red-500 font-bold mb-4">
                            参加には <a href="{{ route('profile.edit') }}" class="text-blue-500 underline">POTZ会員</a> の登録をしてください。
                        </p>
                    @else
                        @if (!$event->is_paid)
                            <button class="bg-blue-200 text-white font-bold py-2 px-4 rounded text-center w-full hover:bg-blue-700">
                                準備中
                            </button>
                        @else
                            @if ($event->is_paid && $event->isUpcoming())
                                @php
                                    $participant = $event->participants()->where('user_id', Auth::id())->first();
                                @endphp

                                @if ($participant)
                                    @if ($participant->status == 0)
                                        <p class="text-gray-600 font-bold mt-2">主催者の確認をお待ちください。</p>
                                    @elseif ($participant->status == 1 && !$event->isOngoing())
                                        <p class="text-orange-600 font-bold mt-2">当日をお待ちください。</p>
                                    @elseif ($participant->status == 1 && $event->isOngoing())
                                        <a href="{{ $event->zoom_url }}" class="bg-orange-500 text-white font-bold py-2 px-4 rounded text-center w-full hover:bg-orange-700">
                                            参加
                                        </a>
                                    @endif
                                @else
                                    <button onclick="openPaymentModal({{ $event->id }})"
                                        class="bg-blue-700 text-white font-bold py-2 px-4 rounded text-center w-full hover:bg-blue-700">
                                        参加予約
                                    </button>
                                @endif
                            @endif
                        @endif
                    @endif

                    {{-- モーダル（デフォルトでは非表示） --}}
                    <div id="paymentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center">
                        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
                            <h2 class="text-lg font-semibold mb-4">支払い方法を選択</h2>
                            <form id="paymentForm">
                                @csrf
                                <input type="hidden" name="event_id" id="modalEventId">
                                <label for="payment_method" class="block text-gray-700">支払い方法:</label>
                                <select name="payment_method" id="payment_method" class="w-full border-gray-300 rounded-lg shadow-sm">
                                    <option value="銀行振込">銀行振込</option>
                                    <option value="PayPay">PayPay</option>
                                    <option value="その他">その他</option>
                                </select>
                                <div class="mt-4 flex justify-end">
                                    <button type="button" onclick="closePaymentModal()"
                                        class="mr-2 bg-gray-400 text-white px-4 py-2 rounded">キャンセル</button>
                                    <button type="button" onclick="submitPayment()"
                                        class="bg-blue-500 text-white px-4 py-2 rounded">申し込む</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- 管理者用の編集・削除ボタン --}}
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
            @endforeach
        </div>

        {{-- ページネーション --}}
        <div class="mt-6 flex justify-center">
            {{ $futureEvents->links() }}
        </div>
    @endif

    {{-- どちらのイベントもない場合 --}}
    @if ($todayEvents->isEmpty() && $futureEvents->isEmpty())
        <p class="col-span-full text-center">現在、イベントの開催予定はありません。</p>
    @endif
</div>
    </body>
</x-app-layout>
<script>
    function openPaymentModal(eventId) {
        document.getElementById('modalEventId').value = eventId;
        document.getElementById('paymentModal').classList.remove('hidden');
    }

    function closePaymentModal() {
        document.getElementById('paymentModal').classList.add('hidden');
    }

    function submitPayment() {
        let eventId = document.getElementById('modalEventId').value;
        let paymentMethod = document.getElementById('payment_method').value;
        let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(`/events/${eventId}/participate`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken
            },
            body: JSON.stringify({ payment_method: paymentMethod })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("参加予約が完了しました！");
                closePaymentModal();
                location.reload();
            } else {
                alert("エラーが発生しました: " + data.error);
            }
        })
        .catch(error => console.error("Error:", error));
    }
    function fetchEventStatus() {
    fetch('/events/status')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            data.events.forEach(event => {
                let button = document.querySelector(`#event-button-${event.id}`);
                if (button) {
                    if (event.isOngoing) {
                        button.classList.remove('bg-blue-500', 'bg-green-600', 'bg-gray-300', 'opacity-50', 'cursor-not-allowed');
                        button.classList.add('bg-orange-500', 'text-white', 'hover:bg-orange-700');
                        button.innerText = 'ここから参加';
                        button.disabled = false;

                        // ボタンが <button> の場合、 <a> に変換
                        if (button.tagName === 'BUTTON') {
                            let newButton = document.createElement('a');
                            newButton.href = event.zoom_url;
                            newButton.innerText = button.innerText;
                            newButton.className = button.className;
                            newButton.setAttribute('id', button.id);
                            newButton.setAttribute('data-event-id', button.getAttribute('data-event-id'));
                            newButton.classList.add('w-full', 'block', 'text-center', 'py-2', 'px-4');
                            button.parentNode.replaceChild(newButton, button);
                        }

                    } else if (event.isFinished) {
                        button.classList.remove('bg-blue-500', 'bg-green-600', 'bg-orange-500', 'hover:bg-orange-700');
                        button.classList.add('bg-gray-300', 'text-gray-600');
                        button.innerText = '終了しました';
                        button.disabled = true;

                        // ボタンが <a> の場合、 <div> に変換
                        if (button.tagName === 'A') {
                            let newDiv = document.createElement('div');
                            newDiv.innerText = button.innerText;
                            newDiv.className = button.className;
                            newDiv.setAttribute('id', button.id);
                            newDiv.setAttribute('data-event-id', button.getAttribute('data-event-id'));
                            newDiv.classList.add('w-full', 'block', 'text-center', 'py-2', 'px-4');
                            button.parentNode.replaceChild(newDiv, button);
                        }

                    } else if (event.isUpcoming) {
                        button.classList.remove('bg-orange-500', 'bg-gray-300');
                        button.classList.add('bg-blue-500', 'text-white', 'opacity-50', 'cursor-not-allowed');
                        button.innerText = '開始をお待ちください';
                        button.disabled = true;
                    }
                }
            });
        })
        .catch(error => console.error("Error fetching event status:", error));
}

// **30秒ごとにイベント状態を更新**
setInterval(fetchEventStatus, 15000);

// **ページロード時に即時実行**
document.addEventListener('DOMContentLoaded', fetchEventStatus);

</script>
