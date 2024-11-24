<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <!-- ログインメッセージ -->
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>

    <!-- 依頼登録リンク -->
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-5 text-3xl text-green-900">
                    <a href="{{ route('requests.index') }}" class="text-blue-500 hover:underline">
                        {{ __('ちょっと助けて依頼') }}
                    </a>
                </div>

            </div>
        </div>
    </div>

    <!-- オンラインイベント -->
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-5 text-3xl text-green-900">
                    <a href="{{ route('events.index') }}" class="text-blue-500 hover:underline">
                        {{ __('オンラインイベント') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
