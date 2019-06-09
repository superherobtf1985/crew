<?php

/**
 * カテゴリがn個あります
 * 各カテゴリはn個の商品を持ちます
 * 商品は月ごとに在庫を確保します
 * 2017/11月 item_a の在庫は100個、とか
 * 商品は日々n個売れたり売れなかったりします
 * 次月の初日、以下のとおりに在庫を確保します
 *   前月に90%以上売れた場合、120%にする
 *   前月に80%以上売れた場合、100%にする（前月の在庫数が100個の場合は100個にする）
 *   前月に60%以上売れた場合、前月頭の20%分を仕入れる
 *   前月に40%以上売れた場合、前月頭の12%分を仕入れる
 *   前月に20%以上売れた場合、前月頭の5%分を仕入れる
 *
 * 結果表示分
 *   4ヶ月分
 *   月ごと
 *     カテゴリごと
 *       商品ごと
 *         在庫数（繰越数と新規確保数）と
 *         1日ごとの売れた数
 *         その月に何個・何%売れたか
 *       何円売れたか
 *     何円売れたか
 *
 * 前月の初期在庫量 = 当初の在庫 + 入荷量
 *  新規入荷量 = 前月の初期在庫量 * 消化率に応じた%
 */

function dump($arg) {
    echo '<pre>';
    var_dump($arg);
    echo '</pre>';
}

function calc_add_stock_count($sold_total_item_count, $stock_count) {
    $first_stock_count_per_month = $sold_total_item_count + $stock_count;

    $sold_ratio = calc_sold_ratio($sold_total_item_count, $first_stock_count_per_month);

    if ($sold_ratio >= 90) {
        return (int)(($first_stock_count_per_month * 1.2) - $stock_count);
    } elseif ($sold_ratio >= 80) {
        return $first_stock_count_per_month - $stock_count;
    } elseif ($sold_ratio >= 60) {
        return (int)($first_stock_count_per_month * 0.2);
    } elseif ($sold_ratio >= 40) {
        return (int)($first_stock_count_per_month * 0.12);
    } elseif ($sold_ratio >= 20) {
        return (int)($first_stock_count_per_month * 0.05);
    } else {
        return 0;
    }
}

function calc_sold_ratio($sold_total_item_count, $first_stock_count_per_month) {
    return round(($sold_total_item_count / $first_stock_count_per_month) * 100, 2);
}

function get_previous_month($year, $month) {
    if ($month === 1) {
        $year--;
        $month = 12;
    } else {
        $month--;
    }

    return [$year, $month];
}

$output_first_year  = 2018;
$output_first_month = 1;
$output_month_count = 4;

$categories = [
    1 => 'category1',
    2 => 'category2',
];

$items = [
    1 => [
        'name'        => 'item_a',
        'price'       => 1000,
        'category_id' => 1,
    ],
    2 => [
        'name'        => 'item_b',
        'price'       => 900,
        'category_id' => 1,
    ],
    3 => [
        'name'        => 'item_c',
        'price'       => 800,
        'category_id' => 2,
    ],
    4 => [
        'name'        => 'item_d',
        'price'       => 1100,
        'category_id' => 2,
    ],
];

$category_items = [];
foreach (array_keys($categories) as $category_id) {
    $category_items[$category_id] = [];

    foreach ($items as $item_id => $item) {
        if ($category_id === $item['category_id']) {
            $category_items[$category_id][$item_id] = $item;
        }
    }
}

$first_item_counts = [
    1 => 100,
    2 => 300,
    3 => 200,
    4 => 80,
];

$stock_counts = $first_item_counts;

$year  = $output_first_year;
$month = $output_first_month;

$months        = [];
$months[$year] = [];

for ($i = 0; $i <= $output_month_count; $i++) {
    $months[$year][] = $month;

    if ($month === 12) {
        $year++;
        $month = 1;

        $months[$year] = [];
    } else {
        $month++;
    }
}

$sold_total_counts      = [];
$added_stock_counts     = [];
$extra_stock_counts     = [];
$item_total_moneys      = [];

foreach (array_keys($items) as $item_id) {
    $sold_counts[$item_id]           = [];
    $sold_total_counts[$item_id]     = [];
    $sold_total_item_count[$item_id] = [];

    foreach ($months as $year => $months_per_year) {
        $sold_counts[$item_id][$year]       = [];
        $sold_total_counts[$item_id][$year] = [];

        foreach ($months_per_year as $month) {
            $sold_counts[$item_id][$year][$month]       = [];
            $sold_total_counts[$item_id][$year][$month] = 0;
        }
    }
}

foreach ($months as $year => $months_per_year) {
    $added_stock_counts[$year] = [];
    $extra_stock_counts[$year] = [];
    $item_total_moneys[$year]  = [];

    foreach ($months_per_year as $month) {
        $added_stock_counts[$year][$month] = [];
        $extra_stock_counts[$year][$month] = [];
        $item_total_moneys[$year][$month]  = [];

        foreach (array_keys($categories) as $category_id) {
            $item_total_moneys[$year][$month][$category_id] = [];
        }
    }
}

foreach ($months as $year => $months_per_year) {
    foreach ($months_per_year as $month) {
        if ($year === $output_first_year && $month === $output_first_month) {
            foreach (array_keys($stock_counts) as $item_id) {
                $added_stock_counts[$year][$month][$item_id] = $first_item_counts[$item_id];
                $extra_stock_counts[$year][$month][$item_id] = 0;
            }
        } else {
            foreach ($stock_counts as $item_id => $stock_count) {
                list ($prev_year, $prev_month) = get_previous_month($year, $month);
                $added_stock_counts[$year][$month][$item_id] = calc_add_stock_count($sold_total_counts[$item_id][$prev_year][$prev_month], $stock_count);

                $extra_stock_counts[$year][$month][$item_id] = $stock_count;
                $stock_counts[$item_id]                      = $stock_count + $added_stock_counts[$year][$month][$item_id];
            }
        }

        $date_count = date("t", mktime(0, 0, 0, $month, 1, $year));

        for ($day = 1; $day <= $date_count; $day++) {
            foreach ($stock_counts as $item_id => $stock_count) {
                $random_number   = mt_rand(7, 13);
                $sold_item_count = mt_rand(0, $stock_count / $random_number);

                $stock_counts[$item_id] -= $sold_item_count;

                $sold_counts[$item_id][$year][$month][$day] = $sold_item_count;
            }
        }

        foreach ($stock_counts as $item_id => $stock_count) {
            $sold_total_item_count = array_sum($sold_counts[$item_id][$year][$month]);

            $sold_total_counts[$item_id][$year][$month] = $sold_total_item_count;

            $item_total_moneys[$year][$month][$items[$item_id]['category_id']][$item_id] = $items[$item_id]['price'] * $sold_total_item_count;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <title>販売</title>
  </head>
  <body>
    <?php foreach ($months as $year => $months_per_year) : ?>
      <?php foreach ($months_per_year as $month) : ?>
        <?php echo $year ?> 年 <?php echo $month ?> 月<br>
        <?php foreach ($category_items as $category_id => $category_item) : ?>
          <b><?php echo $categories[$category_id] ?></b>
          <table border="1">
            <?php foreach ($category_item as $item_id => $item) : ?>
              <tr>
                <td><?php echo $item['name'] ?></td>
                <td>
                <table border="1">
                  <tr>
                    <td>在庫数(繰越数)</td>
                    <td> <?php echo $extra_stock_counts[$year][$month][$item_id] ?> 個</td>
                  </tr>
                  <tr>
                    <td>在庫数(新規確保数)</td>
                    <td> <?php echo $added_stock_counts[$year][$month][$item_id] ?> 個</td>
                  </tr>
                </table>
                <table border="1">
                  <tr>
                    <td>日にち</td>
                    <?php foreach (array_keys($sold_counts[$item_id][$year][$month]) as $day) : ?>
                      <td><?php echo $day ?> 日</td>
                    <?php endforeach ?>
                  </tr>
                  <tr>
                    <td>売れた数</td>
                    <?php foreach ($sold_counts[$item_id][$year][$month] as $sold_count) : ?>
                      <td> <?php echo $sold_count ?> 個</td>
                    <?php endforeach ?>
                  </tr>
                </table>
                <table border="1">
                  <tr>
                    <td>月に売れた合計数</td>
                    <td> <?php echo $sold_total_counts[$item_id][$year][$month] ?> 個</td>
                  </tr>
                  <tr>
                    <td>在庫に対して売れた割合</td>
                    <td>
                      <?php echo calc_sold_ratio($sold_total_counts[$item_id][$year][$month],
                                                ($extra_stock_counts[$year][$month][$item_id] + $added_stock_counts[$year][$month][$item_id]))
                      ?> %
                    </td>
                  </tr>
                  <tr>
                    <td>商品総売り上げ</td>
                    <td> <?php echo $item_total_moneys[$year][$month][$category_id][$item_id] ?> 円</td>
                  </tr>
                </table>
                </td>
              </tr>
            <?php endforeach ?>
            <tr>
              <td>売り上げ</td>
              <td><?php echo array_sum($item_total_moneys[$year][$month][$category_id]) ?> 円</td>
            </tr>
          </table>
          <br>
        <?php endforeach ?>
      <?php endforeach ?>
    <?php endforeach ?>
  </body>
</html>
