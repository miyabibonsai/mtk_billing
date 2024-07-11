<?php

namespace App\Interfaces;

use App\Models\mobile\BillingGroup;
use Carbon\Carbon;

interface BillingInterface
{
    public function __construct($sim, Carbon $date);
    public function model();

    public function createOrGetBillingGroup($user_id,Carbon $date) : BillingGroup;

    public function updateBillingGroupAmount(BillingGroup $billingGroup);

    public function generateBilling();

    public function calculateBillingAmount($sim);
}
