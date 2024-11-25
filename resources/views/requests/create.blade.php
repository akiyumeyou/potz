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

                        <!-- カテゴリ選択 -->
                        <div class="mb-4">
                            <label for="category3_id" class="form-label text-lg font-bold">カテゴリ</label>
                            <select name="category3_id" id="category3_id" class="form-control text-lg" required>
                                <option value="">カテゴリを選択</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->category3 }}</option> <!-- IDを送信 -->
                                @endforeach
                            </select>
                        </div>



                        <!-- 依頼内容 -->
                        <div class="mb-4">
                            <label for="contents" class="form-label text-lg font-bold">依頼内容</label>
                            <textarea name="contents" id="contents" class="form-control text-lg" rows="5" required></textarea>
                        </div>

                        <!-- 日時選択 -->
                        <div class="mb-4">
                            <label for="date" class="form-label text-lg font-bold">希望日時</label>
                            <div class="d-flex align-items-center">
                                <!-- 日付 -->
                                <input type="date" name="date" id="date" class="form-control text-lg me-2" required>

                                <!-- 時間 -->
                                <select name="time_start" id="time_start" class="form-control text-lg" required>
                                    <option value="">時刻を選択</option>
                                    @for ($hour = 8; $hour <= 20; $hour++) <!-- 営業可能時間 -->
                                        <option value="{{ $hour }}:00">{{ $hour }}:00</option>
                                        <option value="{{ $hour }}:30">{{ $hour }}:30</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <!-- 作業時間 -->
                        <div class="mb-4">
                            <label for="time" class="form-label text-lg font-bold">作業時間</label>
                            <select name="time" id="time" class="form-control text-lg" required>
                                <option value="">作業時間を選択</option>
                                @for ($i = 0.5; $i <= 8.0; $i += 0.5) <!-- 30分～8時間 -->
                                    <option value="{{ $i }}">{{ $i }} 時間</option>
                                @endfor
                            </select>
                        </div>

                        <!-- 場所 -->
                        <div class="mb-4">
                            <label for="spot" class="form-label text-lg font-bold">場所</label>
                            <textarea name="spot" id="spot" class="form-control text-lg" rows="2"></textarea>
                        </div>

                        <!-- 住所 -->
                        <div class="mb-4">
                            <label for="address" class="form-label text-lg font-bold">住所</label>
                            <input type="text" name="address" id="address" class="form-control text-lg">
                        </div>

                        <!-- 登録ボタン -->
                        <button type="submit" class="btn btn-success text-lg w-full py-2">依頼を登録</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
