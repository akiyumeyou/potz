<x-app-layout>
    @php
    $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
    @endphp
    @vite(['resources/js/senryu.js'])
    @vite(['resources/js/img.js'])
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('新規投稿') }}
            </h2>
            <a href="{{ route('senryus.index') }}"
               class="px-4 py-2 bg-blue-900 text-white text-sm font-bold rounded shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-gray-300">
                戻る
            </a>
        </div>
    </x-slot>

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
select#theme {
    appearance: none; /* デフォルトの矢印を消す */
    width: 100%;
    font-size: 1.2rem; /* 文字サイズを維持 */
    padding: 1rem 1rem; /* テキストを中央寄せ */
    text-align: center; /* テキストを中央配置 */
    border: 2px solid #555; /* 枠を強調 */
    background-color: white; /* 選択後も視認しやすく */
    border-radius: 8px;
    min-height: 3rem; /* 押しやすいサイズ */
    position: relative;
}

/* 選択後のテキスト */
select#theme:focus {
    background-color: #f8f8f8; /* 軽く色を変えて強調 */
}

/* 矢印を独自に設置 */
select#theme::after {
    content: "▼"; /* 矢印 */
    font-size: 1rem;
    position: absolute;
    top: 10px; /* 上部に配置 */
    right: 15px;
    color: #333;
    pointer-events: none; /* クリックを妨げない */
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

        <div class="content-area">
            @if (session('success'))
                <div>{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div>{{ session('error') }}</div>
            @endif

            <form action="{{ route('senryus.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="senryu-container" >
                    <a>投稿　①テーマを選択　②川柳テキスト入力</a><a>③イメージ画像を選択　④投稿ボタンを押す</a>
                    <select name="theme" id="theme" required>
                        <option value="">テーマ：選択肢を選ぶ</option>
                        <option value="脳活">脳活</option>
                        <option value="体力づくり">体力づくり</option>
                        <option value="日常生活">日常生活</option>
                    </select>

                    <div class="mb-4">
                        <input type="text" name="s_text1" id="s_text1" class="senryu-input w-full p-3 rounded border text-lg required"
                               placeholder="上五 5文字" required maxlength="7">
                    </div>
                    <div class="mb-4">
                        <input type="text" name="s_text2" id="s_text2" class="senryu-input w-full p-3 rounded border text-lg required"
                               placeholder="中七 7文字" required maxlength="8">
                    </div>
                    <div class="mb-4">
                        <input type="text" name="s_text3" id="s_text3" class="senryu-input w-full p-3 rounded border text-lg required"
                               placeholder="下五 5文字" required maxlength="7">
                    </div>
                </div>

                <div id="drop-area">
                    <p>ここにファイルをドラッグ＆ドロップ</p>
                    <input type="file" name="img_path" id="fileElem" class="file-input" accept="image/*,video/*" style="display:none">
                    <label class="file-input-label" for="fileElem">またはファイルを選択</label>
                    <p id="file-name"></p>
                </div>

                <div id="preview-container"></div>

<!-- AI画像生成のボタン -->
<div class="button-container">
    <button type="button" id="ai-generate-toggle" class="ai-generate-btn">AI画像生成で作ってみる</button>
</div>

<!-- AI画像生成用の入力パネル（初期は非表示） -->
<div id="ai-prompt-panel" class="flex justify-center" style="display: none;">
    <!-- テキストエリア -->
     <div class="flex justify-center">
    <textarea id="user_comment" name="user_comment"
        class="w-full max-w-xl h-16 border rounded-lg p-2 text-lg mb-4 text-center"
        placeholder="作りたい画像のイメージをここに書く"></textarea>
    </div>
    <!-- 画像生成実行ボタン -->
     <div class="flex justify-center">
    <button type="button" id="generate-image-btn"
        class="bg-orange-400 text-white font-bold py-4 max-w-xl w-full rounded-lg hover:bg-orange-500 text-lg text-center">
        画像生成
    </button></div>
</div>
<input type="hidden" id="generated_image_name" name="generated_image_name">



                <button type="submit" class="toukou_btn" id="toukou-btn">投稿する</button>
                <button type="button" class="reselect_btn" id="reselect-btn" style="display: none;">画像再選択</button>
            </form>
        </div>
    </body>
</x-app-layout>
