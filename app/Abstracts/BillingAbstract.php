<?php

namespace App\Abstracts;

use App\Interfaces\BillingInterface;
use App\Models\mobile\Billing;
use App\Models\mobile\BillingGroup;
use App\Models\mobile\MerchantPlan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

abstract class BillingAbstract implements BillingInterface
{
    protected $model;
    public function __construct($sim, Carbon $date)
    {
        $this->model = $this->model();
    }

    public function createOrGetBillingGroup($user_id,Carbon $billingdate) : BillingGroup {
        $billing_group = null;
        if($user_id != null) {
            $billing_group = BillingGroup::where('user_id', $user_id)->whereYear('date', $billingdate->format('Y'))->whereMonth('date', $billingdate->format('m'))->first();
        }
        if(is_null($billing_group)) {
            $billing_group = new BillingGroup();
            $billing_group->user_id = $user_id;
            $billing_group->date = $billingdate->format('Y-m-d');
            $billing_group->payment_method_id = 1;
            $billing_group->amount = 0;
            $billing_group->publish = 'no';
            $billing_group->save();
        }
        return $billing_group;
    }

    public function createOrUpdateBilling($group_id ,$sim_id, $user_id, $amount,Carbon $date, ?callable $beforeCreatingBilling = null) {
        $billing = Billing::where('simcard_type', $this->model)->where('simcard_id',$sim_id)->whereYear('date', '=', $date->format('Y'))->whereMonth('date', $date->format('m'))->first();
        Log::info($this->model);
        if(is_null($billing)) {
            $billing = new Billing();
            $billing->simcard_type = $this->model;
            $billing->simcard_id = $sim_id;
            $billing->user_id = $user_id;
            $billing->payment_method_id = 1;
            $billing->status = 'bill';
            $billing->publish='no';
        }
        $billing->simcard_type = $this->model;
        $billing->amount = $amount;
        $billing->date = $date->format('Y-m-d');
        $billing->billing_group_id = $group_id;

        if(!is_null($beforeCreatingBilling)) {
            $beforeCreatingBilling($billing);
        }

        $billing->save();
        return $billing;
    }

    public function generateBillingPrototype(
        $sim, $date,
        ?callable $afterCreatingBillingGroup = null,
        ?callable $beforeCreatingBilling = null,
        ?callable $afterCreatingBilling = null,
        ?callable $afterUpdatingBillingGroup = null
    ) {
        if(get_class($sim) !== $this->model()) {
            throw new \Exception("Class must be an instance of {$this->model()}");
        }
        $billing_group = $this->createOrGetBillingGroup($sim->user_id, $date);

        if(!is_null($afterCreatingBillingGroup)) {
            $afterCreatingBillingGroup($billing_group);
        }

        $billing = $this->createOrUpdateBilling(
            group_id : $billing_group->id,
            sim_id: $sim->id,
            user_id: $sim->user_id,
            amount: $this->calculateBillingAmount($sim),
            date: $date,
            beforeCreatingBilling: $beforeCreatingBilling);

        if(!is_null($afterCreatingBilling)) {
            $afterCreatingBilling($billing);
        }

        $this->updateBillingGroupAmount($billing_group);

        if(!is_null($afterUpdatingBillingGroup)) {
            $afterUpdatingBillingGroup($billing);
        }

        return [
            "billing" => $billing,
            "billing_group" => $billing_group
        ];

    }


    public function updateBillingGroupAmount(BillingGroup $billing_group){
        $amount = $billing_group->billings()->sum('amount');
        $billing_group->amount = $amount;
        $billing_group->save();
    }

    public function getPlanPrice($sim, $model) {
        // $merchant_column = $sim->getMerchantColumn() ?? 'merchant_id';
        // $price = MerchantPlan::where('plannable_id', $sim->$merchant_column)->where('plannable_type', $model)->select('price')->first()->price ?? 0;
        // return $price;
        return 0;
    }
}
