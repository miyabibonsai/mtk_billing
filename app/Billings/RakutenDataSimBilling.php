<?php

namespace App\Billings;

use App\Abstracts\BillingAbstract;
use App\Helpers\System;
use App\Models\RakutenDataSim;
use App\Models\RakutenPlan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class RakutenDataSimBilling extends BillingAbstract
{
    protected RakutenDataSim $sim;
    protected $date;
    public function __construct($sim, Carbon $date)
    {
        parent::__construct($sim, $date);
        $this->sim = $sim;
        $this->date = $date;
    }

    public function model()
    {
        return RakutenDataSim::class;
    }

    public function generateBilling()
    {
        Log::info($this->model);
        $response = $this->generateBillingPrototype($this->sim, $this->date);
        return $response['billing'];
    }

    public function calculateBillingAmount($sim): float
    {
        $plan = RakutenPlan::where('month', 1)->where('gb', $sim->plan)->where('type', 'extension')->first();
        return $plan->price ?? 0;
    }
}
