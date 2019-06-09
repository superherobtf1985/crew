<?php

/**
 * n人の生徒がいます
 * 年にn回(3回)テストが開催されます
 * 教科はn個(算数、国語、理科、社会、英語)あります
 * 点数はランダムで0〜100点
 * ランクを適当に定義する（300〜250=A,249〜200=B,...など）
 *
 * 結果を表示してください
 * 各テストごとの生徒ごとの教科ごとの点数を表で表示
 * 生徒ごとの教科ごとの年間合計点数を表示
 * 上記年間合計点数によるランク表示も合わせて表示
 *
 * 表の形式はいい感じで（見たときに分かりやすく）
 */

function decide_rank($score, $rank_min_scores) {
    foreach ($rank_min_scores as $rank => $rank_min_score) {
        if ($score >= $rank_min_score) {
            return $rank;
        }
    }
}

$tests_count   = 3;
$perfect_score = 100;

$student_names = [
    1 => '一郎',
    2 => '次郎',
    3 => '三郎',
];

$subjects = ['算数', '国語', '理科', '社会', '英語'];

$rank_min_scores = [
    'A' => 250,
    'B' => 200,
    'C' => 150,
    'D' => 100,
    'E' => 50,
    'F' => 0,
];

$total_scores = [];
foreach (array_keys($student_names) as $student_id) {
    $total_scores[$student_id] = [];

    foreach ($subjects as $subject) {
        $total_scores[$student_id][$subject] = 0;
    }
}

$scores = [];
for ($test_number = 1; $test_number <= $tests_count; $test_number++) {
    $scores[$test_number] = [];

    foreach (array_keys($student_names) as $student_id) {
        $scores[$test_number][$student_id] = [];

        foreach ($subjects as $subject) {
            $score = mt_rand(0, $perfect_score);

            $scores[$test_number][$student_id][$subject] = $score;

            $total_scores[$student_id][$subject] += $score;
        }
    }
}

$ranks = [];
foreach (array_keys($student_names) as $student_id) {
    $ranks[$student_id] = [];

    foreach ($subjects as $subject) {
        $ranks[$student_id][$subject] = decide_rank($total_scores[$student_id][$subject], $rank_min_scores);

        if (empty($ranks[$student_id][$subject])) {
            echo 'error on decide_rank';
            exit;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset='utf-8'>
    <title>exam</title>
  </head>
  <body>
    <?php for ($test_number = 1; $test_number <= $tests_count; $test_number++) : ?>
      <table border='1'>
        <tr>
          <td>第 <?php echo $test_number ?> 回</td>
          <?php foreach ($subjects as $subject) : ?>
            <td><?php echo $subject ?></td>
          <?php endforeach ?>
        </tr>
        <?php foreach ($student_names as $student_id => $student_name) : ?>
          <tr>
            <td><?php echo $student_name ?> さん</td>
            <?php foreach ($subjects as $subject) : ?>
              <td><?php echo $scores[$test_number][$student_id][$subject] ?> 点</td>
            <?php endforeach ?>
          </tr>
        <?php endforeach ?>
      </table>
      <br>
    <?php endfor ?>
    <?php foreach ($student_names as $student_id => $student_name) : ?>
      <table border='1'>
        <tr>
          <td><?php echo $student_name ?> さん</td>
          <?php foreach ($subjects as $subject) : ?>
            <td><?php echo $subject ?></td>
          <?php endforeach ?>
        </tr>
        <tr>
          <td>年間合計点数</td>
          <?php foreach ($subjects as $subject) : ?>
            <td><?php echo $total_scores[$student_id][$subject] ?> 点</td>
          <?php endforeach ?>
        </tr>
        <tr>
          <td>ランク</td>
          <?php foreach ($subjects as $subject) : ?>
            <td><?php echo $ranks[$student_id][$subject] ?></td>
          <?php endforeach ?>
        </tr>
      </table>
      <br>
    <?php endforeach ?>
  </body>
</html>
