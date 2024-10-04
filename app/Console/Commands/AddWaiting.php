<?php

namespace App\Console\Commands;

use App\Models\mobile\Simcard;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AddWaiting extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-waiting';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //loyo
        $today = Carbon::now();
        $lastmonth = $today->subMonth();
        $sims = Simcard::whereDate('activation_date', '>=', '2022-09-01')
            ->whereDate('activation_date', '<=', $lastmonth->endOfMonth())
            ->where('status', 'active')
            ->where('merchant', 20)
            ->whereNotNull('plan')
            ->get();
        $data = array();
        for ($i = 0; $i < count($sims); $i++) {
            array_push($data, [
                'sim_id' => $sims[$i]['id'],
                'date' => Carbon::now()->format('Y-m-d'),
                'mc_id' => $sims[$i]['merchant'],
                'sim_status' => 'active',
                'number' => $sims[$i]['tel_no'],
                'plan' => $sims[$i]['plan'],
                'callplan' => $sims[$i]['callplan'],
                'previous_plan' => $sims[$i]['previous_plan'],
                'previous_callplan' => $sims[$i]['previous_callplan'],

            ]);
        }
        \App\Models\mobile\WaitingBillingGenerateSim::insert($data);


        // All simcards
        $sims = Simcard::whereDate('activation_date', '>=', '2022-09-01')
            ->whereDate('activation_date', '<=', Carbon::now()->format('Y-m-d'))->orderBy('id', 'asc')->where('status', 'active')
            ->where('merchant', '<>', 20)
            ->whereNotNull('plan')
            ->get();
        $data = array();
        for ($i = 0; $i < count($sims); $i++) {
            array_push($data, [
                'sim_id' => $sims[$i]['id'],
                'date' => Carbon::now()->format('Y-m-d'),
                'mc_id' => $sims[$i]['merchant'],
                'sim_status' => 'active',
                'number' => $sims[$i]['tel_no'],
                'plan' => $sims[$i]['plan'],
                'callplan' => $sims[$i]['callplan'],
                'previous_plan' => $sims[$i]['previous_plan'],
                'previous_callplan' => $sims[$i]['previous_callplan'],
            ]);
        }


        // Merchant 2
        $sims = Simcard::where('deactivation_date', '!=', null)->whereDate('deactivation_date', '>=', $lastmonth->startOfMonth())->whereDate('deactivation_date', '<=', $lastmonth->endOfMonth())->orderBy('id', 'asc')->where('status', 'deactivate')
            ->whereIn('merchant', array(2))
            ->whereNotNull('plan')
            ->get();


        $data = array();
        for ($i = 0; $i < count($sims); $i++) {
            array_push($data, [
                'sim_id' => $sims[$i]['id'],
                'date' => Carbon::now()->format('Y-m-d'),
                'mc_id' => $sims[$i]['merchant'],
                'sim_status' => 'deactivate',
                'number' => $sims[$i]['tel_no'],
                'plan' => $sims[$i]['plan'],
                'callplan' => $sims[$i]['callplan'],
                'previous_plan' => $sims[$i]['previous_plan'],
                'previous_callplan' => $sims[$i]['previous_callplan'],
            ]);
        }
        \App\Models\mobile\WaitingBillingGenerateSim::insert($data);
    }
}
