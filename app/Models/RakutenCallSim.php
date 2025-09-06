<?php

namespace App\Models;

use App\Billings\RakutenCallSimBilling;
use App\Traits\Billable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RakutenCallSim extends Model
{
    use HasFactory, Billable;
    protected $connection = "connection2";
    protected $merchant_column = 'merchant';
    protected $plan_column = 'plan';

    public function generateBilling(Carbon $date) {
        $billing = new RakutenCallSimBilling($this, $date);
        return $billing->generateBilling();
    }

    public function __construct()
    {
        $this->table = DB::connection($this->connection)->getDatabaseName() . '.rakuten_call_sims';
    }
}
