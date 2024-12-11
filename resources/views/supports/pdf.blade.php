<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>領収書</title>
    <style>
        body {
            font-family: 'noto_sans_jp', sans-serif;
            margin: 40px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .details, .footer {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        .details th, .details td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .details th {
            background-color: #f4f4f4;
        }
        .footer {
            margin-top: 40px;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>領収書</h1>
        <p>発行日: {{ \Carbon\Carbon::now()->format('Y年m月d日') }}</p>
    </div>

    <table class="details">
        <tr>
            <th>項目</th>
            <th>金額</th>
        </tr>
        <tr>
            <td>サポート費用</td>
            <td>{{ number_format($matching->cost * $matching->time, 0) }}円</td>
        </tr>
        <tr>
            <td>交通費</td>
            <td>{{ number_format($matching->transportation_costs, 0) }}円</td>
        </tr>
        <tr>
            <td>その他1</td>
            <td>{{ number_format($matching->sonotacost1, 0) }}円</td>
        </tr>
        <tr>
            <td>その他2</td>
            <td>{{ number_format($matching->sonotacost2, 0) }}円</td>
        </tr>
        <tr>
            <td>その他3</td>
            <td>{{ number_format($matching->sonotacost3, 0) }}円</td>
        </tr>
        <tr>
            <th>合計</th>
            <th>{{ number_format($matching->costkei, 0) }}円</th>
        </tr>
    </table>

    <div class="footer">
        <p>領収書を大切に保管してください。</p>
    </div>
</body>
</html>

