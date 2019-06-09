<?php

// function dump($arg) {
//     echo '<pre>';
//     var_dump($arg);
//     echo '</pre>';
// }

function find_movable_blocks($blocks, $current_block) {
    $movable_blocks = [];

    $row_number    = $current_block['row_number'];
    $column_number = $current_block['column_number'];

    $movable_candidate_blocks = [
        'down'  => ['row_number' => $row_number + 1, 'column_number' => $column_number,    ],
        'up'    => ['row_number' => $row_number - 1, 'column_number' => $column_number,    ],
        'left'  => ['row_number' => $row_number,     'column_number' => $column_number - 1,],
        'right' => ['row_number' => $row_number,     'column_number' => $column_number + 1,],
    ];

    foreach ($movable_candidate_blocks as $movable_candidate_block) {
        if (isset($blocks[$movable_candidate_block['row_number']][$movable_candidate_block['column_number']])) {
            if ($blocks[$movable_candidate_block['row_number']][$movable_candidate_block['column_number']] === '　') {
                $movable_blocks[] = [
                    'row_number'    => $movable_candidate_block['row_number'],
                    'column_number' => $movable_candidate_block['column_number'],
                ];
            }
        }
    }

    return $movable_blocks;
}

function find_specific_block($blocks, $specific_block) {
    foreach ($blocks as $row_number => $row) {
        foreach ($row as $column_number => $block) {
            if ($block === $specific_block) {
                return [
                    'row_number'    => $row_number,
                    'column_number' => $column_number,
                ];
            }
        }
    }
}

function confirm_next_to_end($end_block, $current_block) {
    $row_distance    = abs($end_block['row_number'] - $current_block['row_number']);
    $column_distance = abs($end_block['column_number'] - $current_block['column_number']);

    $distance = $row_distance + $column_distance;

    return ($distance === 1);
}

$map_file = 'map.txt';

$blocks = [];
foreach (file($map_file) as $row) {
    $blocks[] = preg_split('//u', $row, -1, PREG_SPLIT_NO_EMPTY);
}

$start_block = find_specific_block($blocks, '開');
$end_block   = find_specific_block($blocks, '終');

if (empty($start_block)) {
    exit;
}

if (empty($end_block)) {
    exit;
}

$current_block = $start_block;

$branch_blocks = [];

while ($current_block !== $end_block) {
    $movable_blocks = find_movable_blocks($blocks, $current_block);

    if (!empty($movable_blocks)) {
        if (count($movable_blocks) >= 2) {
            array_unshift($branch_blocks, $current_block);
        }

        $current_block = $movable_blocks[array_rand($movable_blocks)];

        $blocks[$current_block['row_number']][$current_block['column_number']] = '＋';

        if (confirm_next_to_end($end_block, $current_block)) {
            $current_block = $end_block;
        }
    } else {
        if (empty($branch_blocks)) {
            exit;
        }

        $current_block = array_shift($branch_blocks);
    }
}

$fp = fopen('map_result.txt', 'w');

foreach ($blocks as $row) {
    foreach ($row as $block) {
        fwrite($fp, $block);
    }
}

fclose($fp);

echo nl2br(file_get_contents('map_result.txt'));

?>
