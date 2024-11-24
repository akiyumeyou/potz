<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('依頼作成') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('requests.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="contents" class="form-label">依頼内容</label>
                            <textarea name="contents" id="contents" class="form-control" rows="5" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="date" class="form-label">希望日付</label>
                            <input type="date" name="date" id="date" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="time" class="form-label">時間</label>
                            <input type="number" name="time" id="time" class="form-control" placeholder="例: 15 (15時)">
                        </div>
                        <div class="mb-3">
                            <label for="spot" class="form-label">場所</label>
                            <textarea name="spot" id="spot" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">住所</label>
                            <input type="text" name="address" id="address" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-success">依頼を登録</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
