<x-app-layout>

    <x-slot name="header">
        <div class="bg-[#FAF3E0] text-center py-4 text-lg font-bold text-gray-800">
            <p>お知らせ</p>
            <ul>
                @forelse (auth()->user()->unreadNotifications as $notification)
                    <li>
                        <a href="{{ route('notifications.read', $notification->id) }}" class="text-blue-600 hover:underline">
                            {{ $notification->data['message'] }}
                        </a>
                    </li>
                @empty
                    <li>現在、お知らせはありません。</li>
                @endforelse
            </ul>

        </div>
    </x-slot>


    <div class="py-6">
        <!-- ボタンレイアウト -->
        <div class="max-w-7xl mx-auto grid grid-cols-2 sm:grid-cols-3 gap-4 px-4">
            <!-- サポート依頼 -->
            <a href="{{ route('requests.index') }}" class="relative block bg-[#F5DEB3] hover:bg-[#F0C27B] border border-gray-300 rounded-lg shadow-md p-4">
                <div class="rounded-lg overflow-hidden">
                    <img src="{{ asset('img/buttons/2.png') }}" alt="サポート依頼" class="mx-auto">
                </div>
            </a>

            <!-- つながるチャットルーム -->
            <div class="relative block bg-[#FFDAB9] hover:bg-[#FFB347] border border-gray-300 rounded-lg shadow-md p-4 opacity-50 cursor-not-allowed">
                <div class="rounded-lg overflow-hidden">
                    <img src="{{ asset('img/buttons/3.png') }}" alt="つながるチャットルーム" class="mx-auto">
                </div>
            </div>

            <!-- オンライン交流 -->
            <a href="{{ route('events.index') }}" class="relative block bg-[#FFF8DC] hover:bg-[#FBE9A1] border border-gray-300 rounded-lg shadow-md p-4">
                <div class="rounded-lg overflow-hidden">
                    <img src="{{ asset('img/buttons/6.png') }}" alt="オンライン交流" class="mx-auto">
                </div>
            </a>

            <!-- シニア動画 -->
            <div class="relative block bg-[#FFF8DC] hover:bg-[#FBE9A1] border border-gray-300 rounded-lg shadow-md p-4 opacity-50 cursor-not-allowed">
                <div class="rounded-lg overflow-hidden">
                    <img src="{{ asset('img/buttons/5.png') }}" alt="シニア動画" class="mx-auto">
                </div>
            </div>

            <!-- シルバー川柳 -->
            <a href="{{ route('senryus.index') }}" class="relative block bg-[#FFF8DC] hover:bg-[#FBE9A1] border border-gray-300 rounded-lg shadow-md p-4">
                <div class="rounded-lg overflow-hidden">
                    <img src="{{ asset('img/buttons/4.png') }}" alt="シルバー川柳" class="mx-auto">
                </div>
            </a>

            <!-- 会員Q&A -->
            <div class="relative block bg-[#FFF8DC] hover:bg-[#FBE9A1] border border-gray-300 rounded-lg shadow-md p-4 opacity-50 cursor-not-allowed">
                <div class="rounded-lg overflow-hidden">
                    <img src="{{ asset('img/buttons/7.png') }}" alt="会員Q&A" class="mx-auto">
                </div>
            </div>
        </div>
    </div>
    <!-- <footer id="footer" class="w-full bg-green-800 text-white text-center p-2 fixed bottom-0">
        <img src="{{ asset('img/logo.png') }}" alt="potz" class="inline-block w-8 h-8">
        <a href="https://potz.jp/" class="text-white underline">https://potz.jp/</a>
    </footer> -->
</x-app-layout>
