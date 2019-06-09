<?php

/**
 * ウルトラソウル
 *
 * ウル / トラ / ソウル をランダムに出力
 * ウルトラソウル　が続いたら「ハイ！」と出力
 * おわり
 */
 // やってることが変わる時は改行を入れましょう

$words = ['そして', '輝く', 'ウル', 'トラ', 'ソウル'];

$expect_word = implode('', $words);

$extracted_words = [];
$combined_word   = [];

while ($combined_word !== $expect_word) {
    $words_idx = array_rand($words);

    echo $words[$words_idx];

    $extracted_words[] = $words[$words_idx];

    $combined_word = implode('', $extracted_words);

    if (count($words) <= count($extracted_words)) {
        array_shift($extracted_words);
    }
}

echo 'ハイ!';
