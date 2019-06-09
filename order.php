<?php

/**
 * 製品A
 *   部品A2個と部品B1個からできています。
 * 製品B
 *   部品C3個と部品D2個からできています。
 * 製品C
 *   部品B1個と部品D1個からできています。
 *
 * 製品Aと製品Bと製品Cをランダムで発注します。
 * 　部品にはそれぞれ在庫がありそれがなくなるまで製造します。
 *
 * 最後に以下を出力します。
 *
 * 製造前の各部品の在庫数
 * 製品の発注数
 * 製造した製品の個数
 * 製造後の各部品の在庫数
 */

function dump($arg) {
    echo '<pre>';
    var_dump($arg);
    echo '</pre>';
}

function check_can_make_product($product_conposition, $stock_item_counts) {
    foreach ($product_conposition as $item_id => $need_item_count) {
        if ($stock_item_counts[$item_id] < $need_item_count) {
            return false;
        }
    }

    return true;
}

$first_item_counts = [
    'a' => 20,
    'b' => 15,
    'c' => 15,
    'd' => 10,
];

$product_conpositions = [
    'A' => [
        'a' => 2,
        'b' => 1,
    ],
    'B' => [
        'c' => 3,
        'd' => 2,
    ],
    'C' => [
        'b' => 1,
        'd' => 1,
    ],
];

$ordered_product_counts = [];

$stock_item_counts = $first_item_counts;

$product_ids = array_keys($product_conpositions);

while (!empty($product_ids)) {
    $product_id = $product_ids[array_rand($product_ids)];

    if (check_can_make_product($product_conpositions[$product_id], $stock_item_counts)) {
        if (!isset($ordered_product_counts[$product_id])) {
            $ordered_product_counts[$product_id] = 0;
        }

        $ordered_product_counts[$product_id]++;

        foreach ($product_conpositions[$product_id] as $item_id => $item_count) {
            $stock_item_counts[$item_id] -= $item_count;
        }
    } else {
        array_shift($product_ids);
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <title>オーダー</title>
  </head>
  <body>
    <table border="1">
      <tr>
        <td>製造前の各部品の在庫数</td>
        <?php foreach ($first_item_counts as $item_id => $item_count) : ?>
        <td><?php echo $item_id ?>:
            <?php echo $item_count ?> 個
        </td>
        <?php endforeach ?>
      </tr>
      <tr>
        <td>製品の発注数</td>
        <td><?php echo array_sum($ordered_product_counts) ?> 個</td>
      </tr>
      <tr>
        <td>製造した製品の個数</td>
        <?php foreach (array_keys($product_conpositions) as $product_id) : ?>
          <?php if (isset($ordered_product_counts[$product_id])) : ?>
            <td>
              <?php echo $product_id ?>:
              <?php echo $ordered_product_counts[$product_id] ?> 個
            </td>
          <?php endif ?>
        <?php endforeach ?>
      </tr>
      <tr>
        <td>製造後の各部品の在庫数</td>
        <?php foreach ($stock_item_counts as $item_id => $item_count) : ?>
        <td><?php echo $item_id ?>:
            <?php echo $item_count ?> 個
        </td>
        <?php endforeach ?>
      </tr>
    </table>
  </body>
</html>
