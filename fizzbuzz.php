<?php

/**
 * FizzBuzz 2
 *
 * 基本仕様はFizzBuzzと同じです
 * ただし、追加で7の倍数の時はBarと出力してください
 * 15や21のように3,5,7のうち複数の倍数の場合は
 * Fizz/Buzz/Barの順番で全て出力すること
 */

for ($i = 1; $i <= 100; $i++) {
    $word = '';

    if (($i % 3) === 0) {
        $word = 'Fizz';
    }
    if (($i % 5) === 0) {
        $word = $word . 'Buzz';
    }
    if (($i % 7) === 0) {
        $word = $word . 'Bar';
    }

    if ($word === '') {
        echo $i;
    } else {
        echo $word;
    }

    echo '<br>';
}
