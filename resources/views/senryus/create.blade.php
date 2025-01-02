<x-app-layout>
    @php
    $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
    @endphp
    @vite(['resources/css/senryu.css', 'resources/js/senryu.js'])

    <body>
        <header>
            <p>新規投稿</p>
        </header>

        <div class="content-area">
            @if (session('success'))
                <div>{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div>{{ session('error') }}</div>
            @endif

            <form action="{{ route('senryus.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="senryu-container">
                    <a>テーマを選択.川柳テキスト入力。</a><br><a>イメージ画像を選択。投稿ボタンを押す</a><br>
                    <label for="theme">テーマ:</label>
                    <select name="theme" id="theme" class="senryu-input" required>
                        <option value="">選択してください</option>
                        <option value="シニア川柳">シニア川柳</option>
                        <option value="ペット自慢">ペット自慢</option>
                        <option value="孫自慢">孫自慢</option>
                        <option value="趣味">趣味</option>
                        <option value="つぶやき">つぶやき</option>
                        <option value="その他">その他</option>
                    </select><br>
                    <label for="s_text1">上五:</label>
                    <input type="text" name="s_text1" id="s_text1" class="senryu-input" required maxlength="5"><br>
                    <label for="s_text2">中七:</label>
                    <input type="text" name="s_text2" id="s_text2" class="senryu-input" required maxlength="7"><br>
                    <label for="s_text3">下五:</label>
                    <input type="text" name="s_text3" id="s_text3" class="senryu-input" required maxlength="5"><br>
                </div>

                <div id="drop-area">
                    <p>ここにファイルをドラッグ＆ドロップ</p>
                    <input type="file" name="img_path" id="fileElem" class="file-input" accept="image/*,video/*" style="display:none">
                    <label class="file-input-label" for="fileElem">またはファイルを選択</label>
                    <p id="file-name"></p>
                </div>

                <div id="preview-container"></div>

                <button type="submit" class="toukou_btn" id="toukou-btn">投稿する</button>
                <button type="button" class="reselect_btn" id="reselect-btn" style="display: none;">画像再選択</button>
            </form>
        </div>
    </body>
</x-app-layout>
