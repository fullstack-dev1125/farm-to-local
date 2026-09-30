<?php

namespace Database\Seeders;

use App\Models\State;
use Illuminate\Database\Seeder;

/** This slice seeds Texas only (launch state). All 50 + the 12 launch flags arrive with Milestone 1. */
class StateSeeder extends Seeder
{
    public function run(): void
    {
        State::updateOrCreate(['code' => 'TX'], ['name' => 'Texas', 'slug' => 'texas', 'launch' => true]);
    }
}
