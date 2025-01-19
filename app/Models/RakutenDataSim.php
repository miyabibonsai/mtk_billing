<?php

namespace App\Models;

use App\Billings\RakutenDataSimBilling;
use App\Traits\Billable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RakutenDataSim extends Model
{
    use HasFactory, Billable;

    protected $connection = 'connection2';
    protected $plan_column = 'plan';

    public function __construct()
    {
        $this->table = DB::connection($this->connection)->getDatabaseName() . '.rakuten_data_sims';
    }

    public function generateBilling(Carbon $date) {
        $billing = new RakutenDataSimBilling($this, $date);
        return $billing->generateBilling();
    }
}
