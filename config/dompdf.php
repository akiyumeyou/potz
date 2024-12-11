<?php

return [
    'font_dir' => storage_path('fonts'), // フォントディレクトリ
    'font_cache' => storage_path('fonts'), // キャッシュディレクトリ
    'default_font' => 'noto_sans_jp', // デフォルトフォント名

    'options' => [
        'isHtml5ParserEnabled' => true,
        'isFontSubsettingEnabled' => true,
    ],

    'custom_fonts' => [
        'noto_sans_jp' => [
            'R'  => 'NotoSansJP-Regular.ttf', // Regular フォント
            'B'  => 'NotoSansJP-Bold.ttf',    // Bold フォント
        ],
    ],
];

