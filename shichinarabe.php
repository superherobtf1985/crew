<?php
/**
 * 七並べをするプログラム
 *
 * トランプが48枚あります
 * 7はすべてゲーム開始時に並べられています
 *
 * 4人でプレイします(1人当たり手札は12枚)
 *
 * プレイヤーは順番にカードを並べていきます
 * 並べられるカードはすでに並べてあるカードの数字と隣り合う数字だけです
 *
 * カードが置けない場合は3回までスキップできます(4回目で失格)
 * 失格になったら手持ちのカードをすべて並べます
 *
 * ゲームを有利に進めるため、カードは7から遠い数字のものを優先的に置いていきます
 *
 * 手札がなくなったらゲームクリアです
 * クリアした順番を出力します
 *
 * 失格の場合は最下位です
 * 失格の時点で、その人の持っていたカードは全て場に置かれます
 * (ただし、7, 8, _, 10 と場にあった時に 11 は置けません)
 * もし、失格が2人以上いた場合は同率最下位です

 * 13の次に1はおけない
 */

 function dump($arg) {
    echo '<pre>';
    var_dump($arg);
    echo '</pre>';
}

$players                 = ['player1', 'player2', 'player3', 'player4'];
$suits                   = ['heart', 'diamond', 'spade', 'club'];
$disqualified_skip_count = 4;

$field_cards = [];
foreach ($suits as $suit) {
    $field_cards[$suit] = [];

    for ($i = 1; $i <= 13; $i++) {
        if ($i === 7) {
            $field_cards[$suit][$i] = true;
        } else {
            $field_cards[$suit][$i] = false;
        }
    }
}

$deck_cards = [];
foreach ($suits as $suit) {
    for ($i = 1; $i <= 13; $i++) {
        if ($i !== 7) {
            $deck_cards[] = [
                'suit'   => $suit,
                'number' => $i,
            ];
        }
    }
}

$hand_cards = [];
foreach ($players as $player) {
    $hand_cards[$player] = [];
}

while (!empty($deck_cards)) {
    foreach ($players as $current_player) {
        if (empty($deck_cards)) {
            break;
        }

        $random_card_index = array_rand($deck_cards);

        $hand_cards[$current_player][] = $deck_cards[$random_card_index];

        unset($deck_cards[$random_card_index]);
    }
}

$finished_players     = [];
$disqualified_players = [];
$skip_counts          = [];

foreach ($players as $player) {
    $skip_counts[$player] = 0;
}

$players_count = count($players);

while ((count($disqualified_players) + count($finished_players)) !== $players_count) {
    foreach ($players as $current_player) {
        $putable_cards = [];

        if (!in_array($current_player, $finished_players) && !in_array($current_player, $disqualified_players)) {
            foreach (array_keys($field_cards) as $suit) {
                for ($number = 6; $number >= 1; $number--) {
                    if (!$field_cards[$suit][$number]) {
                        foreach ($hand_cards[$current_player] as $hand_card) {
                            if ($suit === $hand_card['suit'] && $number === $hand_card['number']) {
                                $putable_cards[] = $hand_card;
                                break;
                            }
                        }
                        break;
                    }
                }

                for ($number = 8; $number <= 13; $number++) {
                    if (!$field_cards[$suit][$number]) {
                        foreach ($hand_cards[$current_player] as $hand_card) {
                            if ($suit === $hand_card['suit'] && $number === $hand_card['number']) {
                                $putable_cards[] = $hand_card;
                                break;
                            }
                        }
                        break;
                    }
                }
            }

            if (!empty($putable_cards)) {
                $most_advantageous_card = null;

                foreach ($putable_cards as $putable_card) {
                    if ($most_advantageous_card === null) {
                        $most_advantageous_card = $putable_card;
                    } elseif (abs($most_advantageous_card['number'] - 7) < abs($putable_card['number'] - 7)) {
                        $most_advantageous_card = $putable_card;
                    }
                }

                $field_cards[$most_advantageous_card['suit']][$most_advantageous_card['number']] = true;

                foreach ($hand_cards[$current_player] as $index => $hand_card) {
                    if ($hand_card === $most_advantageous_card) {
                        unset($hand_cards[$current_player][$index]);

                        if (empty($hand_cards[$current_player])) {
                            $finished_players[] = $current_player;
                        }
                        break;
                    }
                }
            } else {
                $skip_counts[$current_player]++;

                if ($skip_counts[$current_player] === $disqualified_skip_count) {
                    $disqualified_players[] = $current_player;

                    foreach ($hand_cards[$current_player] as $index => $hand_card) {
                        $field_cards[$hand_card['suit']][$hand_card['number']] = true;

                        unset($hand_cards[$current_player][$index]);
                    }
                }
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <title>七ならべ</title>
  </head>
  <body>
    <table border="1">
      <?php foreach ($finished_players as $idx => $player) : ?>
        <tr>
          <td>第 <?php echo $idx + 1 ?> 位</td>
          <td><?php echo $player ?></td>
        </tr>
      <?php endforeach ?>
      <?php foreach ($disqualified_players as $disqualified_player) : ?>
        <tr>
          <td>失格者</td>
          <td><?php echo $disqualified_player ?></td>
        </tr>
      <?php endforeach ?>
    </table>
  </body>
</html>
