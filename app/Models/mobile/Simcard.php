<?php

namespace App\Models\mobile;

use App\Billings\DataSimBilling;
use App\Billings\SimcardBilling;
use App\Traits\Billable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Simcard extends Model
{
    use HasFactory, Billable;

    protected $connection = "connection2";
    protected $merchant_column = 'merchant';
    protected $plan_column = 'plan';


    public function __construct()
    {
        $this->table = DB::connection($this->connection)->getDatabaseName() . '.simcards';
    }

    public function generateBilling(Carbon $date) {
        $billing = new SimcardBilling($this, $date);
        return $billing->generateBilling();
    }

    public function isDiscountPeriod(Carbon $date = null) : bool {
        if($this->isNotLoyo()) return false;

        if(is_null($date)) $date = Carbon::now();
        $last6month = (clone $date)->subMonths(6);
        return Carbon::parse($this->activation_date)->gt($last6month);
    }

    public function isLoyo() {
        return $this->user_type === 'loyo';
    }

    public function isNotLoyo() {
        return $this->user_type !== 'loyo';
    }

    public function isOoak() {
        return $this->user_type === 'ooak';
    }
}
