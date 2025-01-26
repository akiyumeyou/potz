<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-semibold leading-tight text-gray-800">使い方投稿</h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
        <form method="post" action="{{ route('admin.posts.store') }}" enctype="multipart/form-data" class="mt-5 md:mt-0 md:col-span-2">
            @csrf
            <div class="shadow overflow-hidden sm:rounded-md">
                <div class="px-4 py-5 bg-white sm:p-6">
                    <label for="title" class="block text-sm font-medium text-gray-700">タイトル:</label>
                    <input type="text" name="title" id="title" required class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">

                    <label for="content" class="block text-sm font-medium text-gray-700 mt-4">内容:</label>
                    <textarea id="content" name="content" required rows="4" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>

                    <label for="file" class="block text-sm font-medium text-gray-700 mt-4">ファイル（オプション）:</label>
                    <input type="file" id="file" name="file" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>
                <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        投稿する
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>

