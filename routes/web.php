<?php

use App\Models\mobile\Billing;
use App\Models\mobile\WaitingBillingGenerateSim;
use App\Models\RakutenDataSim;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::get('/hello', function () {
    $arr = ["name" => "Jhon Doe", "age" => 12] + ["name" => "Marry Jame"];
    return $arr;
});
Route::get('/', function () {
    return view('welcome');
});
