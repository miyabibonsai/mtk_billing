<?php

namespace App\Billings;

use App\Abstracts\BillingAbstract;
use App\Helpers\System;
use App\Models\mobile\DataSim;
use App\Models\mobile\DataSimPlan;
use Carbon\Carbon;

class DataSimBilling extends BillingAbstract
{
    protected DataSim $sim;
    protected $date;
    public function __construct($sim, Carbon $date)
    {
        parent::__construct($sim, $date);
        $this->sim = $sim;
        $this->date = $date;;
    }
    public function model()
    {
        return  DataSim::class;
    }

    public function generateBilling()
    {
        $this->generateBillingPrototype($this->sim, $this->date);
    }

    public function calculateBillingAmount($sim): float
    {
        return $sim->price ?? 0;
    }
}
