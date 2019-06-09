<?php
/**
 * プレイヤーはN人(N>1)
 * トランプ52枚を順番に2枚ずつめくる
 * 同じ数字だったらもう一度めくる
 * めくるカードが無くなったら終了
 * 違う数字だったら次の人
 * N人目の次の人は1人目
 * J,Q,kは11,12,13でいい
 * 2枚めくって同じ数字だったらめくる対象から除外する
 * 各プレイヤーが取った組の数を出力する
 *
 * 神経衰弱です
 */

 function dump($arg) {
     echo '<pre>';
     var_dump($arg);
     echo '</pre>';
 }

$player_names = [
    1 => 'ケン',
    2 => 'ヒロト',
    3 => 'エミ',
];

$suits = ['spade', 'heart', 'diamond', 'club'];

$cards = [];
foreach ($suits as $suit) {
    for ($i = 1; $i <= 13; $i++) {
        $cards[] = [
            'suit'   => $suit,
            'number' => $i,
        ];
    }
}

$players_count  = count($player_names);
$player_scores  = array_fill(1, $players_count, 0);
$last_player_id = $players_count;

$player_id = 1;
while (count($cards) > 0) {
    $turn_cards = array_rand($cards, 2);

    if ($cards[$turn_cards[0]]['number'] === $cards[$turn_cards[1]]['number']) {
        $player_scores[$player_id]++;

        unset($cards[$turn_cards[0]]);
        unset($cards[$turn_cards[1]]);
    } else {
        if ($player_id === $last_player_id) {
            $player_id = 1;
        } else {
            $player_id++;
        }
    }
}

for ($i = 1; $i <= $last_player_id; $i++) {
    echo $player_names[$i] . ' gets ' . $player_scores[$i] . ' pairs<br>';
}
