<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>領収書</title>

<style>
       @font-face {
    font-family: 'migmix';
    src: url('{{ base_path("storage/fonts/migmix-1p-regular.ttf") }}') format('truetype');
    font-weight: normal;
    font-style: normal;
    }

    @font-face {
        font-family: 'migmix';
        src: url('{{ base_path("storage/fonts/migmix-1p-bold.ttf") }}') format('truetype');
        font-weight: bold;
        font-style: normal;
    }

    body {
        font-family: 'migmix', sans-serif;
    }


        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .details {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        /* .details th, .details td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        } */
        .details th {
        background-color: #f4f4f4;
        font-weight: bold;
        text-align: right; /* ヘッダーは左寄せ */
        }

        .details td {
            text-align: right; /* 金額を右寄せ */
            padding-right: 10px; /* 少し余白を追加 */
        }

        .details th {
            background-color: #f4f4f4;
            font-weight: bold;
        }
        .footer {
            margin-top: 40px;
            text-align: right;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <!-- 領収書ヘッダー -->
    <div class="header">
        <h1>領収書</h1>
        <p>発行日: {{ \Carbon\Carbon::now()->format('Y年m月d日') }}</p>
        <p>依頼ID: {{ $matching->request_id }}</p>
    </div>

    <!-- 詳細テーブル -->
    <table class="details">
        <tr>
            <th>項目</th>
            <th>金額</th>
        </tr>
        <tr>
            <td>サポート費用</td>
            <td>{{ number_format($matching->cost * $matching->time, 0) }} 円</td>
        </tr>
        <tr>
            <td>交通費</td>
            <td>{{ number_format($matching->transportation_costs, 0) }} 円</td>
        </tr>
        <tr>
            <td>その他費用1</td>
            <td>{{ number_format($matching->sonotacost1, 0) }} 円</td>
        </tr>
        <tr>
            <td>その他費用2</td>
            <td>{{ number_format($matching->sonotacost2, 0) }} 円</td>
        </tr>
        <tr>
            <td>その他費用3</td>
            <td>{{ number_format($matching->sonotacost3, 0) }} 円</td>
        </tr>
        <tr>
            <th>合計金額</th>
            <th>{{ number_format($matching->costkei, 0) }} 円</th>
        </tr>
    </table>

    <!-- フッター -->
    <div class="footer">
        <p>領収書を大切に保管してください。</p>
    </div>
</body>
</html>
