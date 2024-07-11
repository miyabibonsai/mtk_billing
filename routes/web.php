<?php

use App\Models\mobile\WaitingBillingGenerateSim;
use App\Models\RakutenDataSim;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $waiting = WaitingBillingGenerateSim::where('simcard_type', RakutenDataSim::class)->count();
    return $waiting;
    return view('welcome');
});
