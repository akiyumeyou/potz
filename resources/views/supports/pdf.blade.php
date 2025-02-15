<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>領収書</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* @font-face {
            font-family: 'migmix';
            src: url('{{ base_path("storage/fonts/migmix-1p-regular.ttf") }}') format('truetype');
        } */
        @font-face {
            font-family: 'migmix';
            src: url('{{ public_path("fonts/migmix-1p-regular.ttf") }}') format("truetype");
        }
        @font-face {
            font-family: 'migmix';
            font-weight: bold;
            src: url('{{ public_path("fonts/migmix-1p-bold.ttf") }}') format("truetype");
        }

        body {
            font-family: 'migmix', sans-serif;
            text-align: center; /* 全体中央寄せ */
        }
        @media print {
            body {
                margin: 0;
                width: 148mm; /* A5縦向き */
                height: 210mm;
            }

        @media (max-width: 768px) {
        body {
            font-size: 12px; /* スマホ向けにフォントサイズを調整 */
        }

        .details {
            width: 60%; /* テーブル幅を縮小 */
        }
}

        }

        /* テーブルの罫線とレイアウト */
        .details {
            width: 60%;
            margin: 0 auto; /* テーブルを中央寄せ */
            border-collapse: collapse;
        }

        .details th, .details td {
            border: 1px solid #000; /* 黒い罫線 */
            padding: 8px; /* セルの余白 */
        }

        .details th {
            background-color: #f4f4f4;
            font-weight: bold;
            text-align: center; /* ヘッダーは中央寄せ */
        }

        .details td {
            text-align: right; /* 数字は右寄せ */
            padding-left: 16px; /* 左余白（文字列のため） */
        }

        .underline {
            text-decoration: underline;
        }

        .logo {
            width: 80px; /* ロゴの幅を指定 */
            margin: 0 auto 20px; /* 上下余白を調整 */
        }
    </style>
</head>
<body class="bg-gray-100 p-4">
    <!-- コンテナ -->
    <div class="max-w-screen-md mx-auto bg-white shadow-md p-6 border border-gray-300">
        <!-- ヘッダー -->
        <div class="text-center mb-6">
            <h1 class="text-8xl font-bold">領収書</h1>
        </div>

        <!-- 宛名 -->
        <div class="mb-6">
            <p class="text-6xl underline">
                <strong>{{ $matching->requester->real_name ?? '不明' }}</strong> 様
            </p>
        </div>

        <!-- 合計金額 -->
        <div class="text-center mb-6">
            <p class="text-5xl font-bold">領収金額: {{ number_format($matching->costkei, 0) }} 円</p>
        </div>

        <!-- 内訳テーブル -->
        <table class="details">
            <thead>
                <tr>
                    <th>項目</th>
                    <th>金額</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: left; padding-left: 16px;">サポート費用</td>
                    <td>{{ number_format($matching->cost * $matching->time, 0) }} 円</td>
                </tr>
                <tr>
                    <td style="text-align: left; padding-left: 16px;">交通費</td>
                    <td>{{ number_format($matching->transportation_costs, 0) }} 円</td>
                </tr>
                <tr>
                    <td style="text-align: left; padding-left: 16px;">その他1</td>
                    <td>{{ number_format($matching->sonotacost1, 0) }} 円</td>
                </tr>
                <tr>
                    <td style="text-align: left; padding-left: 16px;">その他2</td>
                    <td>{{ number_format($matching->sonotacost2, 0) }} 円</td>
                </tr>
                <tr>
                    <td style="text-align: left; padding-left: 16px;">その他3</td>
                    <td>{{ number_format($matching->sonotacost3, 0) }} 円</td>
                </tr>
                <tr>
                    <th>合計金額</th>
                    <th>{{ number_format($matching->costkei, 0) }} 円</th>
                </tr>
            </tbody>
        </table>

        <!-- 領収日 -->
        <div class="mt-6">
            <p class="text-3xl">日付: {{ now()->format('Y年m月d日') }}</p>
            <p class="text-2xl">但：上記金額を領収いたしました。</p>
        </div>

        <!-- サポーター名 -->
        <div class="text-right mt-4">
            <p class="text-3xl">領収者: {{ $matching->supporter->real_name ?? '不明' }}</p>
        </div>
    </div>
    <!-- ロゴ -->
    <div class="text-center">
        <img src="{{ asset('img/logo.png') }}" alt="POTZ" style="width: 40px; height: auto;">
    </div>
</body>
</html>
