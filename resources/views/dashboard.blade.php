<x-app-layout>
    <x-slot name="header">
        <!-- お知らせ用スペース -->
        <div class="bg-[#FAF3E0] text-center py-4 text-lg font-bold text-gray-800">
            <p>POTZからのお知らせ（ダミー表示中）</p>
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
            <div class="relative block bg-[#FFF8DC] hover:bg-[#FBE9A1] border border-gray-300 rounded-lg shadow-md p-4 opacity-50 cursor-not-allowed">
                <div class="rounded-lg overflow-hidden">
                    <img src="{{ asset('img/buttons/4.png') }}" alt="シルバー川柳" class="mx-auto">
                </div>
            </div>

            <!-- 会員Q&A -->
            <div class="relative block bg-[#FFF8DC] hover:bg-[#FBE9A1] border border-gray-300 rounded-lg shadow-md p-4 opacity-50 cursor-not-allowed">
                <div class="rounded-lg overflow-hidden">
                    <img src="{{ asset('img/buttons/7.png') }}" alt="会員Q&A" class="mx-auto">
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
