{{-- resources/views/posts/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('POTZ使い方') }}
            </h2>
            <a href="{{ route('dashboard') }}"
               class="px-4 py-2 bg-blue-900 text-white text-sm font-bold rounded shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-gray-300">
                戻る
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <h1 class="text-2xl font-bold">ご確認ください</h1>
        @foreach ($posts as $post)
            <div class="mt-4 p-4 bg-gray-100 rounded-lg shadow">
                <h2 class="text-lg font-semibold text-gray-700">{{ $post->title }}</h2>
                <p class="mt-2 text-gray-600" id="content-{{ $post->id }}">
                    {{ \Illuminate\Support\Str::limit($post->content, 150, '...') }}
                    <span class="hidden" id="full-content-{{ $post->id }}">{{ $post->content }}</span>
                    <button onclick="toggleContent({{ $post->id }})" class="text-blue-500 hover:text-blue-700">続きを見る</button>
                </p>
                        @if ($post->file_path)
                        <a href="{{ $post->getFileUrl() }}" target="_blank" class="text-blue-500 underline">ファイルを表示</a>
                        @endif
                        @if (auth()->user()->membership_id == 5)
                        <a href="{{ route('admin.posts.edit', $post->id) }}" class="inline-block mt-3 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">編集</a>
                        @endif
            </div>
        @endforeach
    </div>
</x-app-layout>

<script>
function toggleContent(id) {
    var content = document.getElementById('content-' + id);
    var fullContent = document.getElementById('full-content-' + id);
    if (fullContent.classList.contains('hidden')) {
        content.innerHTML = fullContent.textContent + '<button onclick="toggleContent(' + id + ')" class="text-blue-500 hover:text-blue-700">閉じる</button>';
    } else {
        content.innerHTML = '{{ \Illuminate\Support\Str::limit("'+ fullContent.textContent +'", 150, "...") }}' + '<button onclick="toggleContent(' + id + ')" class="text-blue-500 hover:text-blue-700">続きを見る</button>';
    }
    fullContent.classList.toggle('hidden');
}
</script>
