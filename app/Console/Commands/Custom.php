<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\mobile\Billing;
use App\Models\mobile\BillingGroup;
use App\Models\mobile\BillingItem;
use App\Models\mobile\DataSim;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Custom extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:custom';

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
        //             // 'plan'=> $simcard->plan_id,
        //             'simcard_type' => $class_name
        //         ]);
        //     }
        // }
        // \App\Models\mobile\WaitingBillingGenerateSim::insert($data);
        $datasim = DataSim::where('user_id', 5)->first();
        $datasim->generateBilling(Carbon::now());
        $this->info($datasim->id);
        // return 0;
        // $g = BillingGroup::doesntHave('billings')->delete();
        // $this->info($g);
        // $g = BillingGroup::whereHas('billings', function($query){
        //     $query->whereYear('date', "2024")
        //     ->whereMonth('date', "10")
        //     ->datasim();
        // })->count();

        // $i = BillingItem::whereHas('billing', function($query){
        //     $query->whereYear('date', "2024")
        //     ->whereMonth('date', "10")
        //     ->datasim();
        // })->count();
        // $b = Billing::whereYear('date', "2024")
        // ->whereMonth('date', "10")
        // ->datasim()->count();
    }
}
