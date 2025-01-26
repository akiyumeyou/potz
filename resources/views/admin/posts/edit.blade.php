{{-- resources/views/admin/posts/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">投稿を編集</h2>
    </x-slot>

    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="mt-5 md:mt-0 md:col-span-2">
            <form method="post" action="{{ route('admin.posts.update', $post) }}" enctype="multipart/form-data" class="shadow sm:rounded-md sm:overflow-hidden">
                @csrf
                @method('PUT')
                <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">タイトル:</label>
                        <input type="text" name="title" id="title" value="{{ $post->title }}" required class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label for="content" class="block text-sm font-medium text-gray-700">内容:</label>
                        <textarea id="content" name="content" rows="3" required class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ $post->content }}</textarea>
                    </div>
                    <div>
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">更新する</button>
                    </div>
                </div>
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
