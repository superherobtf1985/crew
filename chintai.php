<?php

/**
 * ワンフロアあたりの部屋数と階数は決める
 * 物件を適当に何件か定義する
 * 適当に入居済みの部屋を決める
 * 物件はランダムで空き室がある
 * 1Fのベースの金額を決めて階数が上がってくごとに家賃が1000円上がる
 *
 * 物件は値引き可能な物件とそうでない物件がある
 * 例えば値引き可能額2000円とか
 * 値引き可能物件はオーナーの気分でもうちょい値引きできる
 * 例えば値引き可能額2000円の場合、気分次第で倍の4000円まで値引きできる
 * ランダムで二重値引き (気分で不動産が引いて、更にオーナーが引く感じ)
 *
 * 借り主を適当に定義する
 * 借り主は支払い可能額を持つ（私は10万円まで）
 *
 * 物件・部屋に対して複数の申し込みがあった場合は適当に抽選
 *
 * 結果
 * 物件・部屋に対して申し込んだ人
 * 家賃
 * 誰が借りたか
 * 最終的に物件が見つからなかった人はホームレス
 *  ホームレスの一覧も出す
 */

function dump($arg) {
    echo '<pre>';
    var_dump($arg);
    echo '</pre>';
}

function make_rooms ($houses) {
    $rooms = [];

    foreach (array_keys($houses) as $house_id) {
        $rooms[$house_id] = [];

        for ($floor_number = 1; $floor_number <= $houses[$house_id]['floor_count']; $floor_number++) {
            $rooms[$house_id][$floor_number] = [];

            for ($room_number = 1; $room_number <= $houses[$house_id]['each_floor_rooms_count']; $room_number++) {
                $rooms[$house_id][$floor_number][$room_number] = $room_number;
            }
        }
    }

    return $rooms;
}

function unset_living_rooms ($rooms) {
    foreach ($rooms as $house_id => $house_rooms) {
        foreach ($house_rooms as $floor_number => $floor_rooms) {
            foreach (array_keys($floor_rooms) as $room_number) {
                if (mt_rand(1, 4) <= 3) {
                    unset($rooms[$house_id][$floor_number][$room_number]);
                }
            }
        }
    }

    return $rooms;
}

function calc_discount_money() {
    $discount_money = 0;

    $real_estate_discount = mt_rand(1, 2);

    if ($real_estate_discount <= 1) {
        $discount_money += 2000;
    }

    $owner_discount = mt_rand(1, 2);

    if ($owner_discount <= 1) {
        $discount_money += 2000;
    }

    return $discount_money;
}

function calc_rental_money($base_rental_money, $floor_number, $discount_moneys) {
    $add_floor_money = 1000 * ($floor_number - 1);

    return ($base_rental_money + $add_floor_money - $discount_moneys);
}

$houses = [
    1 => [
        'each_floor_rooms_count' => 4,
        'floor_count'            => 3,
        'base_rental_money'      => 50000,
    ],
    2 => [
        'each_floor_rooms_count' => 4,
        'floor_count'            => 2,
        'base_rental_money'      => 60000,
    ],
];

$rend_candidate_persons = [
    1 => [
        'name'           => '一郎',
        'payable_money'  => 53000,
    ],
    2 => [
        'name'           => '次郎',
        'payable_money'  => 60000,
    ],
    3 => [
        'name'           => '三郎',
        'payable_money'  => 62000,
    ],
];

$rooms       = make_rooms($houses);
$empty_rooms = unset_living_rooms($rooms);

$discount_moneys = [];
foreach (array_keys($empty_rooms) as $house_id) {
    $discount_moneys[$house_id] = calc_discount_money();
}

$rend_candidate_person_ids = [];
foreach (array_keys($rend_candidate_persons) as $person_id) {
    $rend_candidate_person_ids[$person_id] = $person_id;
}

$applied_person_ids = [];
$rental_moneys      = [];
$rent_person_ids    = [];

foreach ($empty_rooms as $house_id => $house_rooms) {
    $applied_person_ids[$house_id] = [];
    $rental_moneys[$house_id]      = [];
    $rent_person_ids[$house_id]    = [];

    foreach ($house_rooms as $floor_number => $floor_rooms) {
        $applied_person_ids[$house_id][$floor_number] = [];
        $rent_person_ids[$house_id][$floor_number]    = [];

        foreach ($floor_rooms as $room_number) {
            $applied_person_ids[$house_id][$floor_number][$room_number] = [];
        }
    }
}

foreach ($empty_rooms as $house_id => $house_rooms) {
    foreach ($house_rooms as $floor_number => $floor_rooms) {
        $rental_moneys[$house_id][$floor_number] = calc_rental_money($houses[$house_id]['base_rental_money'], $floor_number, $discount_moneys[$house_id]);

        foreach ($floor_rooms as $room_number) {
            foreach (array_keys($rend_candidate_person_ids) as $rend_candidate_person_id) {
                if ($rental_moneys[$house_id][$floor_number] <= $rend_candidate_persons[$rend_candidate_person_id]['payable_money']) {
                    $applied_person_ids[$house_id][$floor_number][$room_number][$rend_candidate_person_id] = $rend_candidate_person_id;
                }
            }

            if (!empty($applied_person_ids[$house_id][$floor_number][$room_number])) {
                $rent_person_id = array_rand($applied_person_ids[$house_id][$floor_number][$room_number]);

                $rent_person_ids[$house_id][$floor_number][$room_number] = $rent_person_id;

                unset($rend_candidate_person_ids[$rent_person_id]);

                if (empty($rend_candidate_person_ids)) {
                    break 3;
                }
            }
        }
    }
}

$homeless_person_ids = $rend_candidate_person_ids;

?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <title>賃貸</title>
  </head>
  <body>
    <?php foreach ($applied_person_ids as $house_id => $rooms_each_floor) : ?>
      <?php foreach ($rooms_each_floor as $floor_number => $rooms) : ?>
        <?php foreach (array_keys($rooms) as $room_number) : ?>
          <?php if (!empty($applied_person_ids[$house_id][$floor_number][$room_number])) : ?>
            申し込んだ人(
            物件 <?php echo $house_id ?> ,
            <?php echo $floor_number ?> 階 ,
            <?php echo $room_number ?> 号室 )
            <?php foreach ($applied_person_ids[$house_id][$floor_number][$room_number] as $applied_person_id) : ?>
              <?php echo $rend_candidate_persons[$applied_person_id]['name'] ?> さん
            <?php endforeach ?>
            <br>
            家賃: <?php echo $rental_moneys[$house_id][$floor_number] ?> 円<br>
            借りた人: <?php echo $rend_candidate_persons[$rent_person_ids[$house_id][$floor_number][$room_number]]['name'] ?> さん<br><br>
          <?php endif ?>
        <?php endforeach ?>
      <?php endforeach ?>
    <?php endforeach ?>
    <br>
    <?php if (!empty($homeless_person_ids)) : ?>
      ホームレスになった人:
      <?php foreach ($homeless_person_ids as $homeless_person_id) : ?>
      <?php echo $rend_candidate_persons[$homeless_person_id]['name'] ?>さん
      <?php endforeach ?>
    <?php endif ?>
  </body>
</html>
