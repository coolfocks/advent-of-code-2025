<?php

require_once __DIR__ . '/vendor/autoload.php';

/**
 * Setup
 */

define('DEBUG', false);

$file = __DIR__ . '/input.txt';
$contents = file_get_contents($file);
$lines = explode(PHP_EOL, $contents);

enum Direction: string {
    case Left = 'L';
    case Right = 'R';
}

/**
 * @param string $line
 * @return object
 */
function transformLineToDirection(string $line): object {
    $instruction = $line[0];
    $value = str_replace(['L', 'R'], ['', ''], $line);
    return (object) [
        'direction' => Direction::tryFrom($instruction),
        'value' => (int) $value
    ];
}

/**
 * @param integer $position
 * @param Direction $direction
 * @param integer $value
 * @return object
 */
function moveDial(int $position, Direction $direction, int $value): object {
    if (DEBUG) {
        echo 'Dial is at ' . $position . ' .. rotating ' . $direction->value . $value . PHP_EOL;
    }
    $howManyClicksAtZero = 0;
    $i = 1;
    // budem točit
    while ($i <= $value) {
        // doleva logika
        if ($direction === Direction::Left) {
            if ($position === 0) {
                $position = 99;
            }
            else {
                $position -= 1;
            }
        }
        // doprava logika
        if ($direction === Direction::Right) {
            if ($position === 99) {
                $position = 0;
            }
            else {
                $position += 1;
            }
        }
        if ($position === 0) {
            $howManyClicksAtZero++;
        }
        $i++;
    }
    return (object) [
        'position' => $position,
        'zero_clicks' => $howManyClicksAtZero
    ];
}

$tasks = [];
foreach ($lines as $line) {
    $tasks[] = transformLineToDirection($line);
}

unset($file, $contents, $lines);

/**
 * Solution
 */
$dial = 50;
$howManyTimesIsDialAtZero = 0;
$howManyTimesIsDialAtZeroBonus = 0;
foreach ($tasks as $task) {
    $move = moveDial(
        position: $dial,
        direction: $task->direction,
        value: $task->value
    );
    $dial = $move->position;
    $howManyTimesIsDialAtZeroBonus += $move->zero_clicks;
    if ($dial === 0) {
        $howManyTimesIsDialAtZero++;
    }
}

echo 'Dial was at position 0 a total of ' . $howManyTimesIsDialAtZero . ' times.' . PHP_EOL;
echo 'Bonus hits at position 0 during turning ' . $howManyTimesIsDialAtZeroBonus . PHP_EOL;
