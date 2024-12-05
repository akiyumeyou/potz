<?php

if (!function_exists('formatLinks')) {
    function formatLinks($text)
    {
        // 正規表現を使ってリンクを自動的に `<a>` タグで囲む
        $text = preg_replace(
            '/(https?:\/\/[^\s]+)/',
            '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>',
            $text
        );
        return $text;
    }
}
