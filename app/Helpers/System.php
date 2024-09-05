<?php

namespace App\Helpers;

use App\Models\mobile\MerchantPlan;

class System
{
    public static function getPlanPrice(string $model, int $plan_id, $merchant_id){
        // $merchant_plan = MerchantPlan::merchantId($merchant_id)->where('plannable_type', $model)->where('plannable_id', $plan_id)->select('price')->first();
        // if(!is_null($merchant_plan)) return $merchant_plan->price;

        return $model::find($plan_id)->price ?? 0;
    }
}
