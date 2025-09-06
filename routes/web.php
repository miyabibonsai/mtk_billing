<?php

use App\Models\mobile\Billing;
use App\Models\mobile\SimcardB;
use App\Models\mobile\WaitingBillingGenerateSim;
use App\Models\RakutenCallSim;
use App\Models\RakutenDataSim;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::get('/hello', function () {
    SimcardB::find(1)->generateBilling(Carbon::now());
    // RakutenCallSim::find(2)->generateBilling(Carbon::now());
    $arr = ["name" => "Jhon Doe", "age" => 12] + ["name" => "Marry Jame"];
    return $arr;
});
Route::get('/', function () {
    return view('welcome');
});
