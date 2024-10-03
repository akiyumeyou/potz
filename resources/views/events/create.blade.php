<x-app-layout>
    <body class="bg-gray-100">
        <div class="container mx-auto px-4 py-8">
            <h1 class="text-3xl font-bold mb-6">イベントを作成</h1>

            @if (session('success'))
                <div class="alert alert-success bg-green-100 text-green-800 p-4 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded shadow-md">
                @csrf
                <div class="mb-4">
                    <label for="title" class="block text-gray-700 font-bold mb-2">タイトル:</label>
                    <input type="text" name="title" id="title" class="w-full border-gray-300 rounded-lg shadow-sm" required>
                </div>

                <div class="mb-4">
                    <label for="event_date" class="block text-gray-700 font-bold mb-2">開催日:</label>
                    <input type="date" name="event_date" id="event_date" class="w-full border-gray-300 rounded-lg shadow-sm" required>
                </div>

                <div class="mb-4 flex space-x-4">
                    <div class="flex-1">
                        <label for="start_time" class="block text-gray-700 font-bold mb-2">開始時間:</label>
                        <input type="time" name="start_time" id="start_time" class="w-full border-gray-300 rounded-lg shadow-sm" required>
                    </div>
                    <div class="flex-1">
                        <label for="end_time" class="block text-gray-700 font-bold mb-2">終了時間:</label>
                        <input type="time" name="end_time" id="end_time" class="w-full border-gray-300 rounded-lg shadow-sm" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="content" class="block text-gray-700 font-bold mb-2">内容:</label>
                    <textarea name="content" id="content" class="w-full border-gray-300 rounded-lg shadow-sm" rows="4" required></textarea>
                </div>

                <div class="mb-4">
                    <label for="zoom_url" class="block text-gray-700 font-bold mb-2">Zoom URL:</label>
                    <input type="url" name="zoom_url" id="zoom_url" class="w-full border-gray-300 rounded-lg shadow-sm" required>
                </div>

                <div class="mb-4">
                    <label for="recurring_type" class="block text-gray-700 font-bold mb-2">開催頻度:</label>
                    <select name="recurring_type" id="recurring_type" class="w-full border-gray-300 rounded-lg shadow-sm">
                        <option value="once">今回のみ</option>
                        <option value="weekly">毎週</option>
                        <option value="biweekly">隔週</option>
                        <option value="monthly">毎月</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="image" class="block text-gray-700 font-bold mb-2">イメージ画像:</label>
                    <input type="file" name="image" id="image" class="w-full border-gray-300 rounded-lg shadow-sm">
                </div>

                <button type="submit" class="bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-700">作成</button>
            </form>
        </div>
    </body>
</x-app-layout>
