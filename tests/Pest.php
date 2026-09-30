<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/** Run $callback and return how many SQL queries it issued. */
function countQueries(callable $callback): int
{
    $count = 0;
    DB::listen(function () use (&$count) {
        $count++;
    });
    $callback();

    return $count;
}
