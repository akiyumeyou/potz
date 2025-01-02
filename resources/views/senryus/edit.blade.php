<x-app-layout>
    @php
    $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
    @endphp
    @vite(['resources/css/senryu.css', 'resources/js/senryu.js'])
<body>
    <header>
        <p>川柳編集</p>
    </header>

    <div>
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ route('senryus.update', $senryu->id) }}" method="POST" enctype="multipart/form-data" data-type="edit">
            @csrf
            @method('PUT')
            <div class="senryu-container">
                <label for="theme">テーマ:</label>
                <input type="text" name="theme" id="theme" class="senryu-input" value="{{ $senryu->theme }}" required><br>
                <label for="s_text1">上五:</label>
                <input type="text" name="s_text1" id="s_text1" class="senryu-input" value="{{ $senryu->s_text1 }}" required maxlength="5"><br>
                <label for="s_text2">中七:</label>
                <input type="text" name="s_text2" id="s_text2" class="senryu-input" value="{{ $senryu->s_text2 }}" required maxlength="7"><br>
                <label for="s_text3">下五:</label>
                <input type="text" name="s_text3" id="s_text3" class="senryu-input" value="{{ $senryu->s_text3 }}" required maxlength="5"><br>
            </div>

            <div id="drop-area">
                <p>ここにファイルをドラッグ＆ドロップ</p>
                <input type="file" name="img_path" id="fileElem" class="file-input" accept="image/*,video/*" style="display:none">
                <label class="file-input-label" for="fileElem">またはファイルを選択</label>
                <p id="file-name"></p>
            </div>

            <div id="preview-container">
                @if ($senryu->img_path)
                    @if (Str::endsWith($senryu->img_path, ['.mp4', '.mov', '.avi']))
                        <video src="{{ asset($senryu->img_path) }}" controls class="preview"></video>
                    @else
                        <img src="{{ asset($senryu->img_path) }}" class="preview">
                    @endif
                @else
                    <img src="{{ asset('storage/senryus/dummy.jpg') }}" class="senryu-media">
                @endif
            </div>


            <div class="button-container">
                <button type="submit" class="toukou_btn" id="toukou-btn">更新</button>
                <button type="button" class="reselect_btn" id="reselect-btn" style="display: none;">画像再選択</button>
            </div>
        </form>

        <form action="{{ route('senryus.destroy', $senryu->id) }}" method="POST" onsubmit="return confirm('本当に削除しますか？');" class="mt-4">
            @csrf
            @method('DELETE')
            <button type="submit" class="delete_btn">削除</button>
        </form>
    </div>

</body>
</x-app-layout>
