{{-- resources/views/admin/posts/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">投稿を編集</h2>
    </x-slot>

    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="mt-5 md:mt-0 md:col-span-2">
            <form action="{{ route('admin.posts.update', $post->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="title" class="block text-gray-700 font-bold mb-2">タイトル:</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $post->title) }}" required
                        class="w-full border-gray-300 rounded-lg shadow-sm">
                </div>

                <div class="mb-4">
                    <label for="content" class="block text-gray-700 font-bold mb-2">内容:</label>
                    <textarea name="content" id="content" required
                        class="w-full border-gray-300 rounded-lg shadow-sm">{{ old('content', $post->content) }}</textarea>
                </div>

                @if ($post->file_path)
                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">現在のファイル:</label>
                        <a href="{{ asset('storage/' . $post->file_path) }}" target="_blank"
                            class="text-blue-500 underline">現在のファイルを表示</a>
                    </div>
                @endif

                <div class="mb-4">
                    <label for="file" class="block text-gray-700 font-bold mb-2">新しいファイル（任意）:</label>
                    <input type="file" name="file" id="file" class="w-full border-gray-300 rounded-lg shadow-sm">
                </div>

                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    更新
                </button>
            </form>

        </div>

        <div class="mt-5 md:mt-0 md:col-span-2">
            <form method="post" action="{{ route('admin.posts.destroy', $post) }}" onsubmit="return confirm('本当に削除しますか？');" class="shadow sm:rounded-md sm:overflow-hidden">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    削除する
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
