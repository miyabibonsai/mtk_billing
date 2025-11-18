<?php

namespace App\Billings;

use App\Abstracts\BillingAbstract;
use App\Helpers\System;
use App\Models\mobile\Billing;
use App\Models\mobile\BillingItem;
use App\Models\mobile\MobileOption;
use App\Models\mobile\MobilePlan;
use App\Models\mobile\TypeBCallLog;
use App\Models\mobile\SimcardB;
use App\Models\mobile\SimcardBOption;
use App\Models\mobile\SimcardBPlan;
use App\Models\RakutenCallSim;
use App\Settings\BillingSettings;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class SimcardBBilling extends BillingAbstract
{
    protected $billmonth;
    protected $premonth;
    protected $year;
    protected $pre;
    protected $billing_items = [];
    protected $user_id;
    protected $date;
    protected $selectedPlan;
    protected $selectedOption;
    protected Billing $billing;
    protected $planPrice = 0;
    protected $optionPrice = 0;
    protected $billing_settings;
    protected $config;
    protected $bill_unit_column;
    public function __construct($sim, Carbon $date)
    {
        parent::__construct($sim, $date);
        $this->date = $date;
        $this->pre = (clone $date)->subMonth(1);
        Log::info($this->date);
        $this->premonth = $this->pre;
        $this->year = $this->pre->format('Y');
        $this->billing_settings =  app(BillingSettings::class);
        $this->config = config('simcard-b');
        $this->bill_unit_column =  'billunit_60';
        // $this->bill_unit_column = $this->billing_settings->bill_unit == 60 ? 'billunit_60' : 'billunit';
    }
    public function model()
    {
        return SimcardB::class;
    }

    public function generateBilling()
    {
        $response = $this->generateBillingPrototype(
            sim: $this->sim,
            date: $this->date,

            beforeCreatingBilling: function($billing) {
                $billing->setting_options = $this->config;
            },

            afterCreatingBilling: function (Billing $billing)  {
                $this->billing = $billing;
                $this->user_id = $billing->user_id;

                if($this->sim->rewrite || true) {
                    BillingItem::where('billing_id', $billing->id)->delete();
                }


                $this->generateBillingItems();

                Log::info("Saving Billing Amount");
                $this->billing->amount = $this->getTotal();
                $this->billing->total = $this->billing->amount;
                $this->billing->save();

            }
        );
        return $response['billing'];
    }

    public function generateBillingItems()
    {
        Log::info("Generating Billing Items");

        $this->generateBillForPlan();

        $this->generateBillForOptions();

        $this->generateBillForCallLogs();

        Log::info($this->billing_items);

        if(count($this->billing_items)) {
            Log::info("Inserting Billing Items");
            BillingItem::insert($this->billing_items);
        }
    }

    public function generateCallLogWithOption(Carbon $month)
    {
        $call_unit = TypeBCallLog::whereYear('date', '=', $this->year)->whereMonth('date', '=', $month->format('m'))->whereNotIn('type', array('sms', 'forein sms', 'forein call','promo foreign call'))->where('simcard_id', $this->sim->id)->sum($this->bill_unit_column);
        return $call_unit;
    }


    public function pushBillingItem($name, $description, $price, $options = []) {
        if($price > 0) {
            $this->billing_items[] = [
                "billing_id" => $this->billing->id,
                "name" => $name,
                "description" => $description,
                "price" => $price,
                "user_id" => $this->user_id,
                "setting_options" => json_encode($options)
            ];
        }
    }


    public function generateBillForPlan(){
        Log::info("Generating Biling For Plan");
        $selectedPlan = SimcardBPlan::where('gb',$this->sim->plan)->where('mc',$this->sim->mc)->first();
        if($selectedPlan) {
            $this->setPlan($selectedPlan) ;
            $this->pushBillingItem($selectedPlan->name,$selectedPlan->name, $selectedPlan->price);
        }

    }

    public function generateBillForOptions() {
        Log::info("Generating Biling For Options");
        if($this->selectedPlan) {
            $selectedOption = SimcardBOption::where('call_value', $this->sim->callplan)->where('plan_id',$this->selectedPlan->id)->first();
            if($selectedOption) {
                $this->pushBillingItem($selectedOption->option_name, $selectedOption->option_name, $selectedOption->price);
            }
        }
    }

    public function generateBillForCallLogs() {
        Log::info("Generating Billing For Call Logs");
        Log::info($this->premonth);
        $calls = TypeBCallLog::whereYear('date', '=', $this->premonth->year)->whereMonth('date', '=', $this->premonth->format('m'))->where('type', 'call')->where('simcard_id', $this->sim->id)->sum($this->bill_unit_column);
        Log::info($calls);
        Log::info($this->pre);
        if($calls > 0) {
            $this->pushBillingItem('Call', 'Call', $calls * $this->config['call_unit'], ['call_unit' => $this->config['call_unit']]);
        }
        //find sms
        $sms = TypeBCallLog::whereYear('date', '=', $this->premonth->year)->whereMonth('date', '=', $this->premonth->format('m'))->where('type', 'sms')->where('simcard_id', $this->sim->id)->count();
        if ($sms > 0) {
            $this->pushBillingItem('SMS', 'SMS Description', $sms * $this->config['sms_price'], ['sms_unit' => $this->config['sms_price']]);
        }
    }

    public function calculateBillingAmount($sim): float
    {
        return 0;
    }



    protected function setPlan(SimcardBPlan $plan) : void {
        $this->selectedPlan = $plan;
    }

    protected function setOption(MobileOption $mobileOption) : void {
        $this->selectedOption = $mobileOption;
    }

    protected function planExists() : bool {
        return !is_null($this->selectedPlan);
    }

    protected function optionExists() : bool {
        return !is_null($this->selectedOption);
    }

    protected function setPlanPrice() : void {
        if(!$this->planExists()) {
            throw new Exception("Cannot access plan price before setting plan");
        }
        $this->planPrice = System::getPlanPrice(MobilePlan::class, $this->selectedPlan->id, $this->sim->merchant);
    }

    protected function setOptionPrice() : void {
        if(!$this->optionExists()) {
            throw new Exception("Cannot access option price before setting option");
        }
        $this->planPrice = System::getPlanPrice(MobileOption::class, $this->selectedOption->id, $this->sim->merchant);
    }

    public function getTotal()
    {
        $total = BillingItem::where('billing_id', $this->billing->id)->sum('price');
        return $total;
    }
}
