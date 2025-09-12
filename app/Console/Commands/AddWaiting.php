<?php

namespace App\Console\Commands;

use App\Models\mobile\DataSim;
use App\Models\mobile\Simcard;
use App\Models\mobile\SimcardB;
use App\Models\RakutenCallSim;
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
        // $today = Carbon::now();
        // $lastmonth = $today->subMonth();
        // $sims = Simcard::whereDate('activation_date', '>=', '2022-09-01')
        //     ->whereDate('activation_date', '<=', $lastmonth->endOfMonth())
        //     ->where('status', 'active')
        //     ->where('merchant', 20)
        //     ->whereNotNull('plan')
        //     ->get();
        // $this->info(count($sims));
        // $data = array();
        // for ($i = 0; $i < count($sims); $i++) {
        //     array_push($data, [
        //         'sim_id' => $sims[$i]['id'],
        //         'date' => Carbon::now()->format('Y-m-d'),
        //         'mc_id' => $sims[$i]['merchant'],
        //         'sim_status' => 'active',
        //         'number' => $sims[$i]['tel_no'],
        //         'plan' => $sims[$i]['plan'],
        //         'callplan' => $sims[$i]['callplan'],
        //         'previous_plan' => $sims[$i]['previous_plan'],
        //         'previous_callplan' => $sims[$i]['previous_callplan'],
        //         'rewrite' => 'yes'
        //     ]);
        // }
        // \App\Models\mobile\WaitingBillingGenerateSim::insert($data);


        // // All simcards
        // $sims = Simcard::whereDate('activation_date', '>=', '2022-09-01')
        //     ->whereDate('activation_date', '<=', Carbon::now()->format('Y-m-d'))->orderBy('id', 'asc')->where('status', 'active')
        //     ->where('merchant', '<>', 20)
        //     ->whereNotNull('plan')
        //     ->get();
        // $this->info(count($sims));
        // $data = array();
        // for ($i = 0; $i < count($sims); $i++) {
        //     array_push($data, [
        //         'sim_id' => $sims[$i]['id'],
        //         'date' => Carbon::now()->format('Y-m-d'),
        //         'mc_id' => $sims[$i]['merchant'],
        //         'sim_status' => 'active',
        //         'number' => $sims[$i]['tel_no'],
        //         'plan' => $sims[$i]['plan'],
        //         'callplan' => $sims[$i]['callplan'],
        //         'previous_plan' => $sims[$i]['previous_plan'],
        //         'previous_callplan' => $sims[$i]['previous_callplan'],
        //         'rewrite' => 'yes'
        //     ]);
        // }
        // \App\Models\mobile\WaitingBillingGenerateSim::insert($data);

        // // // // Merchant 2
        // $sims = Simcard::where('deactivation_date', '!=', null)->whereDate('deactivation_date', '>=', $lastmonth->startOfMonth())->whereDate('deactivation_date', '<=', $lastmonth->endOfMonth())->orderBy('id', 'asc')->where('status', 'deactivate')
        //     ->whereIn('merchant', array(2))
        //     ->whereNotNull('plan')
        //     ->get();
        // $this->info(count($sims));
        // $data = array();
        // for ($i = 0; $i < count($sims); $i++) {
        //     array_push($data, [
        //         'sim_id' => $sims[$i]['id'],
        //         'date' => Carbon::now()->format('Y-m-d'),
        //         'mc_id' => $sims[$i]['merchant'],
        //         'sim_status' => 'deactivate',
        //         'number' => $sims[$i]['tel_no'],
        //         'plan' => $sims[$i]['plan'],
        //         'callplan' => $sims[$i]['callplan'],
        //         'previous_plan' => $sims[$i]['previous_plan'],
        //         'previous_callplan' => $sims[$i]['previous_callplan'],
        //         'rewrite' => 'yes'
        //     ]);
        // }
        // \App\Models\mobile\WaitingBillingGenerateSim::insert($data);

        // $simcards = DataSim::generateable()->select( 'id','status', 'tel_no', 'plan_name', 'user_id', 'merchant_id', 'user_type', 'plan_id')->get();
        // $this->info("Simcards count ====> ". $simcards->count());

        // $class_name = DataSim::class;
        // $date = Carbon::now()->format('Y-m-d');

        // $data = [];
        // foreach($simcards->chunk(500) as $chunk) {
        //     foreach($chunk as $simcard) {
        //         array_push($data, [
        //             'sim_id' => $simcard->id,
        //             'date' => $date,
        //             'mc_id' => $simcard->merchant_id ?? 0,
        //             'sim_status'=> $simcard->status,
        //             'number'=> $simcard->tel_no,
        //             'plan'=> $simcard->plan_id,
        //             'simcard_type' => $class_name,
        //             'rewrite' => true
        //         ]);
        //     }
        // }
        // \App\Models\mobile\WaitingBillingGenerateSim::insert($data);
        // return 0;

        $simcards = RakutenCallSim::where('status', 'active')->get();
        $this->info("Simcards count ====> ". $simcards->count());

        $class_name = RakutenCallSim::class;
        $date = Carbon::now()->format('Y-m-d');

        $data = [];
        foreach($simcards->chunk(500) as $chunk) {
            foreach($chunk as $simcard) {
                array_push($data, [
                    'sim_id' => $simcard->id,
                    'date' => $date,
                    'mc_id' => $simcard->merchant_id ?? 0,
                    'sim_status'=> $simcard->status,
                    'number'=> $simcard->tel_no,
                    'plan'=> $simcard->plan_id,
                    'simcard_type' => $class_name,
                    'rewrite' => true
                ]);
            }
        }
        \App\Models\mobile\WaitingBillingGenerateSim::insert($data);
        return 0;
    }
}
