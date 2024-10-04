<?php

namespace App\Billings;

use App\Abstracts\BillingAbstract;
use App\Helpers\System;
use App\Models\mobile\Billing;
use App\Models\mobile\BillingItem;
use App\Models\mobile\DataSim;
use App\Models\mobile\DataSimPlan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DataSimBilling extends BillingAbstract
{
    protected $date;
    public function __construct($sim, Carbon $date)
    {
        parent::__construct($sim, $date);
        $this->date = $date;
    }
    public function model()
    {
        return DataSim::class;
    }

    public function generateBilling()
    {
        $this->generateBillingPrototype(
            sim: $this->sim,
            date: $this->date,
            afterCreatingBilling : function( Billing $billing) {
                BillingItem::where('billing_id', $billing->id)->delete();
                $plan = DataSimPlan::find($this->sim->plan_id);
                if($plan) {
                    $item = [
                        "billing_id" => $billing->id,
                        "name" => $plan->name,
                        "description" => ".",
                        "price" => $plan->price ?? 0,
                        "user_id" => $this->sim->user_id ?? null,
                    ];
                    BillingItem::create($item);
                }
            }
        );
    }

    public function calculateBillingAmount($sim): float
    {
        Log::info($sim);
        return (int) (DataSimPlan::find($sim->plan_id)->price ?? 0);
    }
}
