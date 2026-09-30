<?php

use App\Http\Controllers\CityResultsController;
use Illuminate\Support\Facades\Route;

// City results (docs/03): /texas/austin?radius=25|50|100&cat=&verified=1&sort=
Route::get('/{state}/{city}', CityResultsController::class)
    ->where(['state' => '[a-z]+(?:-[a-z]+)*', 'city' => '[a-z]+(?:-[a-z]+)*'])
    ->name('city.results');
