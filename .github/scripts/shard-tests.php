<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

/**
 * Prints a `--filter` regular expression selecting one shard of a Pest test suite.
 *
 * Usage: php .github/scripts/shard-tests.php <index>/<total> [pest arguments...]
 *
 * Pest 4's own `--shard` only discovers classes in the `Tests\` namespace, silently skipping every
 * app-module test, so this partitions all test classes itself. Remove once on Pest 5 (fixed there).
 */
if (! preg_match('/^(\d+)\/(\d+)$/', $argv[1] ?? '', $shard) || $shard[1] < 1 || $shard[1] > $shard[2]) {
    fwrite(STDERR, "Usage: php {$argv[0]} <index>/<total> [pest arguments...]\n");

    exit(1);
}

[, $index, $total] = array_map(intval(...), $shard);

$command = [PHP_BINARY, 'vendor/bin/pest', ...array_slice($argv, 2), '--list-tests'];

$process = proc_open($command, [1 => ['pipe', 'w']], $pipes);

$output = stream_get_contents($pipes[1]);

fclose($pipes[1]);

if (proc_close($process) !== 0) {
    fwrite(STDERR, "Listing tests failed:\n{$output}");

    exit(1);
}

// Pest prefixes listed class names with a `P\` that is not part of the class name `--filter` matches against.
preg_match_all('/^ - (?:P\\\\)?([^:\s]+)::/m', $output, $matches);

$testCounts = array_count_values($matches[1]);

if ($testCounts === []) {
    fwrite(STDERR, "No tests found:\n{$output}");

    exit(1);
}

// Biggest classes first onto the least-loaded shard, with ties broken by name so every shard computes the same split.
uksort($testCounts, fn (string $a, string $b): int => [$testCounts[$b], $a] <=> [$testCounts[$a], $b]);

$shardClasses = array_fill(0, $total, []);
$shardTestCounts = array_fill(0, $total, 0);

foreach ($testCounts as $class => $count) {
    $smallest = array_search(min($shardTestCounts), $shardTestCounts, true);

    $shardClasses[$smallest][] = $class;
    $shardTestCounts[$smallest] += $count;
}

$classes = $shardClasses[$index - 1];

if ($classes === []) {
    fwrite(STDERR, "Shard {$index} of {$total} has no test classes; use fewer shards.\n");

    exit(1);
}

fwrite(STDERR, sprintf(
    "Shard %d of %d: %d of %d classes, %d of %d tests.\n",
    $index,
    $total,
    count($classes),
    count($testCounts),
    $shardTestCounts[$index - 1],
    array_sum($testCounts),
));

// Anchored and case-sensitive so a class name never also matches a longer or differently-cased one.
echo '/^(?:' . implode('|', array_map(fn (string $class): string => preg_quote($class, '/'), $classes)) . ')::/';
