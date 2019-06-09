<?php

/**
 * ある商品群があります
 * 商品は名前と金額
 *
 * ある買う人がいます
 * 名前と都道府県
 *
 * その人が商品をN個買います
 * 買う商品はランダムで購入数もランダム 1個以上
 *
 * 送料は500円
 * ただ、購入数が5以上の場合は1000円
 * ただ、沖縄県と北海道はプラス1000円
 *
 * 買った商品ごとの商品名、個数と、金額
 * 小計、消費税、送料（消費税かからない）、合計金額
 */

function dump($arg) {
    echo '<pre>';
    var_dump($arg);
    echo '</pre>';
}

function choose_buy_products($products) {
    $product_ids = array_keys($products);

    shuffle($product_ids);

    $buy_products_count = mt_rand(1, count($products));

    return array_slice($product_ids, 1, $buy_products_count);
}

$tax_rate                = 0.08;
$nomal_ships_fee         = 500;
$over_count_fee          = 1000;
$specific_prefecture_fee = 1000;

$products = [
    1 => [
        'name'  => '筆箱',
        'price' => 1500,
    ],
    2 => [
        'name'  => '鉛筆',
        'price' => 150,
    ],
    3 => [
        'name'  => 'ホチキス',
        'price' => 200,
    ],
    4 => [
        'name'  => 'ボールペン',
        'price' => 800,
    ],
];

$buyer = [
    'name'       => '太郎',
    'prefecture' => '沖縄',
];

$bought_counts = [];
foreach (array_keys($products) as $product_id) {
    $bought_counts[$product_id] = null;
}

$buy_product_ids = choose_buy_products($products);

foreach ($buy_product_ids as $buy_product_id) {
    $bought_counts[$buy_product_id] = mt_rand(1, 5);
}

$subtotal_moneys = [];
foreach ($buy_product_ids as $buy_product_id) {
    $ships_fee = $nomal_ships_fee;

    if (array_sum($bought_counts) >= 5) {
        $ships_fee += $over_count_fee;
    }

    if ($buyer['prefecture'] === '沖縄' || $buyer['prefecture'] === '北海道') {
        $ships_fee += $specific_prefecture_fee;
    }

    $subtotal_moneys[$buy_product_id] = $bought_counts[$buy_product_id] * $products[$buy_product_id]['price'];
}

$total_money = array_sum($subtotal_moneys);
$tax_price   = $total_money * $tax_rate;

?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <title>ショッピング</title>
  </head>
  <body>
    <table border="1">
      <tr>
        <td>名前</td>
        <td><?php echo $buyer['name'] ?></td>
      </tr>
      <tr>
        <td>都道府県</td>
        <td><?php echo $buyer['prefecture'] ?></td>
      </tr>
        <?php foreach ($buy_product_ids as $buy_product_id) : ?>
          <tr>
            <td><?php echo $products[$buy_product_id]['name'] ?></td>
            <td>
              <table border="1">
                <tr>
                  <td>購入数</td>
                  <td> <?php echo $bought_counts[$buy_product_id] ?> 個</td>
                </tr>
                <tr>
                  <td>その商品の小計</td>
                  <td> <?php echo $subtotal_moneys[$buy_product_id] ?> 円</td>
                </tr>
              </table>
            </td>
          </tr>
        <?php endforeach ?>
      <tr>
        <td>購入商品総数</td>
        <td> <?php echo array_sum($bought_counts) ?> 個</td>
      </tr>
      <tr>
        <td>商品総計額</td>
        <td> <?php echo $total_money ?> 円</td>
      </tr>
      <tr>
        <td>税</td>
        <td><?php echo $tax_price ?>円</td>
      </tr>
      <tr>
        <td>送料</td>
        <td><?php echo $ships_fee ?>円</td>
      </tr>
      <tr>
        <td>支払い金額</td>
        <td><?php echo $total_money + $tax_price + $ships_fee ?> 円</td>
      </tr>
    </table>
  </body>
</html>
