<?php

namespace App\Billings;

use App\Abstracts\BillingAbstract;
use App\Helpers\System;
use App\Models\mobile\Billing;
use App\Models\mobile\BillingItem;
use App\Models\mobile\CallLog;
use App\Models\mobile\MobileOption;
use App\Models\mobile\MobilePlan;
use App\Models\mobile\Simcard;
use App\Settings\BillingSettings;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class SimcardBilling extends BillingAbstract
{
    protected $billmonth;
    protected $premonth;
    protected $year;
    protected $pre;
    protected $billing_items;
    protected $user_id;
    protected $date;
    protected $selectedPlan;
    protected $selectedOption;
    protected Billing $billing;
    protected $planPrice = 0;
    protected $optionPrice = 0;
    protected $billing_settings;
    protected $bill_unit_column;
    public function __construct($sim, Carbon $date)
    {
        parent::__construct($sim, $date);
        $this->date = $date;
        $this->pre = (clone $date)->subMonth(1);
        Log::info($this->date);
        $this->premonth = $this->pre->format('m');
        $this->year = $this->pre->format('Y');
        $this->billing_settings =  app(BillingSettings::class);
        $this->bill_unit_column =  'billunit_60';
        // $this->bill_unit_column = $this->billing_settings->bill_unit == 60 ? 'billunit_60' : 'billunit';
    }
    public function model()
    {
        return Simcard::class;
    }

    public function generateBilling()
    {
        $response = $this->generateBillingPrototype(
            sim: $this->sim,
            date: $this->date,

            beforeCreatingBilling: function($billing) {
                $billing->setting_options = (array) $this->billing_settings;
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

        if(count($this->billing_items)) {
            Log::info("Inserting Billing Items");
            BillingItem::insert($this->billing_items);
        }
    }

    public function generateCallLogWithOption($month)
    {
        $call_unit = CallLog::whereYear('date', '=', $this->year)->whereMonth('date', '=', $month)->whereNotIn('type', array('sms', 'forein sms', 'forein call','promo foreign call'))->where('simcard_id', $this->sim->id)->sum($this->bill_unit_column);
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
        $selectedPlan = MobilePlan::where('gb',$this->sim->plan)->where('mc',$this->sim->mc)->first();
        $this->setPlan($selectedPlan);
        if($this->planExists()) {
            $this->setPlanPrice();
            if($this->sim->isLoyo()) {
                $this->calculateOnLoyo();
            } else {
                $this->calculateOnOoak();
            }
        }
    }

    public function generateBillForOptions() {

        Log::info("Generating Biling For Options");

        $selectedOption = MobileOption::where('call_value', $this->sim->callplan)->where('mobile_plan_id',$this->selectedPlan->id)->first();
        Log::info($this->sim->callplan);
        Log::info($this->selectedPlan);
        $this->setOption($selectedOption);
        $this->setOptionPrice();
        if ($this->optionExists()) {
            //5 min
            $call_unit = $this->generateCallLogWithOption( $this->premonth);
            //get call logs
            if ($call_unit > 0) {
                $this->pushBillingItem('Call', 'Call', $call_unit * 22);
            }

            $this->pushBillingItem($this->selectedOption->option_name, $this->selectedOption->option_name, $this->selectedOption->price);
        }

        /** Generate Last Month Option Billing for Loyo card */
        $activate_month = Carbon::parse($this->sim->activation_date)->format('m');
        if($this->sim->user_type == 'loyo' && $this->premonth == $activate_month)
        {
            $lastMontOption = MobileOption::where('call_value', $this->sim->previous_callplan)->where('mobile_plan_id',$this->selectedPlan->id)->first();
            if($lastMontOption != null)
            {
                $lastMontOptionPrice = System::getPlanPrice(MobileOption::class, $lastMontOption->id, $this->sim->merchant);
                $this->pushBillingItem($lastMontOption->option_name, $lastMontOption->option_name, $lastMontOptionPrice);
            }
        }
    }

    public function calculateOnOoak() {
        $this->pushBillingItem( $this->selectedPlan->name, $this->selectedPlan->name, $this->planPrice );
    }

    public function calculateOnLoyo() {
        //if activate date is prev month
        $discountPrice = $this->selectedPlan->discount;
        if($this->pre->isSameMonth($this->sim->activation_date))
        {
            $daysInmonth = $this->pre->daysInMonth();
            $days = $daysInmonth - Carbon::parse($this->sim->activation_date)->format('d') ;
            $planprice = $this->planPrice - $discountPrice;
            $price = ($planprice/$daysInmonth*$days);
            $premonthName = $this->pre->format('M');
            $this->pushBillingItem($this->selectedPlan->name .' for '.$premonthName , $this->selectedPlan->name .' for '. $premonthName, $price);

        }elseif($this->sim->isDiscountPeriod($this->pre))
        {
            $planprice = $this->planPrice - $discountPrice;
            $premonthName = $this->pre->format('M');
            $this->pushBillingItem($this->selectedPlan->name .' with discount ', $this->selectedPlan->name .' with discount', $planprice);

        }else {
            // $this->addBillingItem($bill, $this->selectedPlan->name , $this->selectedPlan->name , $this->selectedPlan->price, $this->sim->user_id);
            $this->pushBillingItem($this->selectedPlan->name , $this->selectedPlan->name , $this->planPrice);
        }
    }

    public function generateBillForCallLogs() {
        Log::info("Generating Biling For Cal Logs");
        //find sms
        $sms = CallLog::whereYear('date', '=', $this->year)->whereMonth('date', '=', $this->premonth)->where('type', 'sms')->where('simcard_id', $this->sim->id)->count();
        if ($sms > 0) {
            $this->pushBillingItem('SMS', 'SMS Description', $sms * $this->billing_settings->sms_unit, ['sms_unit' => $this->billing_settings->sms_unit]);
        }

        $sms = CallLog::whereYear('date', '=', $this->year)->whereMonth('date', $this->premonth)->where('type', 'forein sms')->where('simcard_id', $this->sim->id)->sum('duration');
        if ($sms > 0) {
            $this->pushBillingItem('国際SMS', '国際SMS Description', $sms * $this->billing_settings->foreign_sms_unit, ['foreign_sms_unit' => $this->billing_settings->foreign_sms_unit]);
        }
        $foreign_call = CallLog::whereYear('date', '=', $this->year)->whereMonth('date', $this->premonth)->where('type', 'forein call')->where('simcard_id', $this->sim->id)->sum($this->bill_unit_column);
        if ($foreign_call > 0) {
            $this->pushBillingItem('Foreign Call', 'Foreign Call Description', $foreign_call * $this->billing_settings->foreign_call_unit, ['foreign_call_unit' => $this->billing_settings->foreign_call_unit]);
        }

        $proforeign_call = CallLog::whereYear('date', '=', $this->year)->whereMonth('date', $this->premonth)->where('type', 'promo foreign call')->where('simcard_id', $this->sim->id)->get();
        if ($proforeign_call !== null) {
            $cost = 0;
            $cost_reason = '';
            foreach ($proforeign_call as $p) {
                $cost += $p->cost;
                $cost_reason = $p->cost_reason;
            }
            $this->pushBillingItem('Promotion Foreign Call',  $cost_reason, $cost);
        }
    }

    public function calculateBillingAmount($sim): float
    {
        return 0;
    }



    protected function setPlan(MobilePlan $plan) : void {
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
