<?php
/**
 * 箱
 *  大きさ　10~20
 *  空き容量1未満で発送
 *  100個
 *
 * 荷物
 *  大きさ　1~5
 *  無限で出てくる
 *
 * ストック場
 *  荷物が入らない場合、ここに置く
 *  箱に入れられる荷物がある場合は、最初にここから入れられるだけ入れる
 *
 * 終了条件
 * 　発送数100
 *
 */


$ships_count = 100;

$package_min_size = 10;
$package_max_size = 20;

$laggage_min_size = 1;
$laggage_max_size = 5;

$shipped_packages    = [];
$stock_laggage_sizes = [];
$shipped_count       = 0;

while ($shipped_count < $ships_count) {
    $package                  = [];
    $package['size']          = mt_rand($package_min_size, $package_max_size);
    $package['luggage_sizes'] = [];

    $package_free_space = $package['size'];

    while ($package_free_space > 0) {
        if (!empty($stock_laggage_sizes)) {
            foreach ($stock_laggage_sizes as $index => $stock_laggage_size) {
                if ($package_free_space >= $stock_laggage_size) {
                    $package_free_space -= $stock_laggage_size;

                    $package['luggage_sizes'][] = $stock_laggage_size;

                    unset($stock_laggage_sizes[$index]);

                    if ($package_free_space === 0) {
                        break;
                    }
                }
            }
        }

        $laggage_size = mt_rand($laggage_min_size, $laggage_max_size);

        if ($package_free_space >= $laggage_size) {
            $package_free_space -= $laggage_size;

            $package['luggage_sizes'][] = $laggage_size;
        } else {
            $stock_laggage_sizes[] = $laggage_size;
        }
    }

    $shipped_packages[] = $package;

    $shipped_count++;
}

?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <title>packing</title>
  </head>
  <body>
    <?php foreach ($shipped_packages as $index => $shipped_package) : ?>
      発送数: <?php echo $index + 1 ?><br>
      箱の大きさ: <?php echo $shipped_package['size'] ?><br>
      荷物の大きさ:
      <?php foreach ($shipped_package['luggage_sizes'] as $luggage_size) : ?>
        <?php echo $luggage_size ?> ,
      <?php endforeach ?>
      <br>
    <?php endforeach ?>
  </body>
</html>
