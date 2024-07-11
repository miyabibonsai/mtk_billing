<?php

namespace App\Traits;

trait Billable
{
    public function getPlanColumn()
    {
        return $this->plan_column ?? "plan_id";
    }

    public function getMerchantColumn()
    {
        return $this->merchant_column ?? "merchant_id";
    }
}
