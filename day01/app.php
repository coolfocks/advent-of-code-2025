<?php

require_once __DIR__ . '/vendor/autoload.php';

/**
 * Setup
 */

define('DEBUG', false);

$file = __DIR__ . '/example.txt';
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
 * @return integer
 */
function moveDial(int $position, Direction $direction, int $value): int {
    if (DEBUG) {
        echo 'Dial is at ' . $position . ' .. rotating ' . $direction->value . $value . PHP_EOL;
    }
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
        $i++;
    }
    return $position;
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
foreach ($tasks as $task) {
    $dial = moveDial(
        position: $dial,
        direction: $task->direction,
        value: $task->value
    );
    if ($dial === 0) {
        $howManyTimesIsDialAtZero++;
    }
}

echo 'Dial was at position 0 a total of ' . $howManyTimesIsDialAtZero . ' times.' . PHP_EOL;
