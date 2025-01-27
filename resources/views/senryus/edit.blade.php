<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('川柳編集') }}
            </h2>
            <a href="{{ route('senryus.index') }}"
               class="px-4 py-2 bg-blue-900 text-white text-sm font-bold rounded shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-gray-300">
                戻る
            </a>
        </div>
    </x-slot>
    @php
    $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
    @endphp
    @vite(['resources/js/senryu.js'])
    <style>
        .senryu-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            margin: 0 auto; /* 左右中央寄せ */
            writing-mode: vertical-rl; /* 縦書き */
            text-orientation: upright;
            margin-top: 10px;
            gap: 10px;
        }

        .senryu-input {
            width: 100%; /* 入力枠を大きくする */
            padding: 10px; /* 内側の余白を増やす */
            margin-bottom: 5px; /* 下側の余白を増やす */
            box-sizing: border-box; /* paddingを含めたサイズでwidthを計算 */
            font-size: 32px;
            writing-mode: vertical-rl;
            text-orientation: upright;
            padding: 10px;
        }
        .theme-container {
            display: flex;
            flex-direction: column;
            align-items: center; /* 水平方向中央寄せ */
            margin: 10px; /* テーマと川柳入力部分の間の余白 */

        }

        #drop-area {
            width: 80%; /* ドロップエリアを少し小さくする */
            height: 300px;
            margin: auto; /* 中央揃え */
            padding: 5px;
            font-size: 20px;
            border: 2px dashed #f2c487; /* 点線のスタイル */
            text-align: center;
            position: relative;
        }

        #drop-area:hover {
            background-color: #e9b013;
        }

        .file-input-label {
            color: blue;
            text-decoration: underline;
            cursor: pointer;
        }

        #file-name {
            margin-top: 10px;
        }

        .toukou_btn, .delete_btn {
            background-color: green;
            font-size: 30px; /* フォントサイズを大きく */
            color: rgb(241, 249, 243);
            padding: 20px 20px; /* スマートフォンでタップしやすいサイズに */
            border-radius: 5px;
            display: block; /* ボタンをブロックレベル要素として扱う */
            margin: 10px auto; /* 左右中央揃え */
            cursor: pointer;
            width: 200px;
        }

        .toukou_btn:hover, .delete_btn:hover {
            background-color: #e9b013; /* ホバー色を変更 */
        }

        .reselect_btn {
            background-color: blue;
            font-size: 30px; /* フォントサイズを大きく */
            color: rgb(241, 249, 243);
            padding: 20px 20px; /* スマートフォンでタップしやすいサイズに */
            border-radius: 5px;
            display: block; /* ボタンをブロックレベル要素として扱う */
            margin: 10px auto; /* 左右中央揃え */
            cursor: pointer;
            width: 200px;
        }

        .reselect_btn:hover {
            background-color: #4a90e2; /* ホバー色を変更 */
        }

        .preview {
            max-width: 100%;
            max-height: 300px; /* プレビュー画像の最大高さを制限 */
            display: block;
            margin: 10px auto;
        }

        @media (max-width: 768px) {
            .senryu-text, .iine {
                font-size: 36px;
            }
            .fieldset {
                max-width: 100%; /* コンテンツの最大幅を制限 */
                margin: auto; /* 中央寄せ */
            }
            #drop-area {
                height: 200px;
            }
        }

        .button-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }
        </style>
    <body>
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
                <input type="text" name="s_text1" id="s_text1" class="senryu-input" value="{{ $senryu->s_text1 }}" required maxlength="7">
                <input type="text" name="s_text2" id="s_text2" class="senryu-input" value="{{ $senryu->s_text2 }}" required maxlength="8">
                <input type="text" name="s_text3" id="s_text3" class="senryu-input" value="{{ $senryu->s_text3 }}" required maxlength="7"><br>
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
