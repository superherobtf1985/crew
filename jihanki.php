<?php

/**
 * エナジードリンク 150円
 * 炭酸飲料水 140円
 * スポーツドリンク 130円
 * 缶コーヒー 120円
 * ミネラルウォーター 110円
 *
 * X円投入する(X > 0)
 * 投入できるのは1000円札、500円硬貨、100円硬貨、50円硬貨、10円硬貨のみ
 * 10000円札、5000円札、2000円札、5円硬貨、1円硬貨は使用不可
 * 紙幣、硬貨の最大数はY枚とする(Y > 0)
 *
 * ランダムで飲料を購入する
 * ただし、飲料の合計金額がNを超えてはならない
 * 各飲料の在庫数はZ本とする(Z > 0)
 *
 * 任意の金額N円(1000,500,100,50,10円(の組み合わせで成立する額))を
 * 1回のみ自販機に投入して、
 * ランダムに何か買ってゆく。
 * それが何本でもいいし、何を買ってもいい。
 * まだ何か買えたとしても、どこで打ち切るかもランダム。
 *
 * 購入したら投入金額、各飲料の本数とその合計金額、全飲料の合計金額、おつりを表示する
 */

$money_kinds = [
    1 => [
        'price'               => 1000,
        'use_max_money_count' => 6,
    ],

    2 => [
        'price'               => 500,
        'use_max_money_count' => 4,
    ],

    3 => [
        'price'               => 100,
        'use_max_money_count' => 3,
    ],

    4 => [
        'price'               => 50,
        'use_max_money_count' => 5,
    ],

    5 => [
        'price'               => 10,
        'use_max_money_count' => 5,
    ],
];

$drinks = [
    1 => [
        'name'  => 'エナジードリンク',
        'price' => 150,
    ],
    2 => [
        'name'  => '炭酸飲料水',
        'price' => 140,
    ],
    3 => [
        'name'  => 'スポーツドリンク',
        'price' => 130,
    ],
    4 => [
        'name'  => 'コーヒー',
        'price' => 120,
    ],

    5 => [
        'name'  => '水',
        'price' => 110,
    ],
];

$stock_counts = [
    1 => 5,
    2 => 6,
    3 => 4,
    4 => 5,
    5 => 7,
];

$usable_money = 0;
foreach ($money_kinds as $money_kind) {
    $usable_money += $money_kind['price'] * rand(0, $money_kind['use_max_money_count']);
}

$used_money = $usable_money;

$bought_counts = array_fill(1, count($drinks), 0);

for ($i = 0; $i < rand(1, 20); $i++) {
    $buy_candidate_drink_id = array_rand($drinks);

    if (($stock_counts[$buy_candidate_drink_id] > 0) && ($usable_money >= $drinks[$buy_candidate_drink_id]['price'])) {
        $drink_id = $buy_candidate_drink_id;

        $bought_counts[$drink_id]++;
        $stock_counts[$drink_id]--;
        $usable_money -= $drinks[$drink_id]['price'];
    }
}

$bought_subtotal_moneys = [];
foreach ($drinks as $drink_id => $drink) {
    $bought_subtotal_moneys[$drink_id] = $drink['price'] * $bought_counts[$drink_id];
}

$bought_total_money = array_sum($bought_subtotal_moneys);

?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <title>自販機</title>
  </head>
  <body>
    <table border="1">
      <tr>
        <td>投入金額</td>
        <td><?php echo $used_money ?>円</td>
      </tr>
      <?php foreach ($drinks as $drink_id => $drink) : ?>
        <?php if ($bought_counts[$drink_id] > 0) : ?>
          <tr>
            <td><?php echo $drink['name'] ?></td>
            <td><?php echo $bought_counts[$drink_id] ?>本</td>
          </tr>
          <tr>
            <td>小計</td>
            <td><?php echo $bought_subtotal_moneys[$drink_id] ?>円</td>
          </tr>
        <?php endif ?>
      <?php endforeach ?>
      <tr>
        <td>全飲料の合計金額</td>
        <td><?php echo $bought_total_money ?>円</td>
      </tr>
      <tr>
        <td>おつり</td>
        <td><?php echo ($used_money - $bought_total_money) ?>円</td>
      </tr>
    </table>
  </body>
</html>
