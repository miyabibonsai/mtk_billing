<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\mobile\Billing;
use App\Models\mobile\BillingGroup;
use App\Models\mobile\BillingItem;
use App\Models\mobile\CallLog;
use App\Models\mobile\DataSim;
use App\Models\mobile\Simcard;
use App\Models\mobile\TypeBCallLog;
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
        $premonth = Carbon::now()->subMonth();
        $calls = TypeBCallLog::whereYear('date', '=', $premonth->year)->whereMonth('date', '=', $premonth->format('m'))->where('type', 'call')->where('simcard_id', 41)->sum('bill_unit60');
        // $this
        // $proforeign_call = CallLog::whereYear('date', '=', '2025')->whereMonth('date', '07')->where('type', 'promo foreign call')->get('simcard_id')->pluck('simcard_id');
        // $this->info("Count: " . $proforeign_call);
        // $billingGroups  = BillingGroup::whereYear('date', 2025)
        // ->whereMonth('date', 8)
        // ->delete();
        // $this->info("Billing Groups count: " . $billingGroups);
        // $items = BillingItem::whereHas('billing', function($query) {
        //     $query->whereYear('date', 2025)
        //     ->whereMonth('date', 8);
        // })->delete();
        // $this->info("Items count: " . $items);
        // $billings = Billing::whereYear('date', 2025)
        //     ->whereMonth('date', 8)
        //     ->delete();
        // $this->info("Billings count: " . $billings);
        // $sim = Simcard::find(8801);
        // $this->info($sim);
        // $sim->generateBilling(Carbon::now());
        // $sims = Simcard::where('user_type', 'loyo')->whereMonth('activation_date', '01')->whereYear('activation_date', '<>', 2025)->select('id', 'tel_no')->get();
        // foreach($sims as $sim) {
        //     $this->info($sim->tel_no);
        //     $this->info($sim->id);
        // }
        // generateBilling
        // $counts = DataSim::whereHas('billings', function($query) {
        //     // $this->info(Carbon::now()->month());
        //     // $this->info(Carbon::now()->year());
        //     $query->whereMonth('date', "01")->whereYear('date', "2025");
        // })->count();
        // $mustCounts = DataSim::generateable()->count();
        // $this->info($counts);
        // $this->info($mustCounts);

        // $callLogs = CallLog::where('type', 'promo foreign call')
        //     ->whereBetween('date', ['2025-05-01', '2025-05-31'])
        //     ->select('simcard_id', DB::raw('count(*) as total'))
        //     ->groupBy('simcard_id')
        //     ->get();
        // $this->info($callLogs->pluck('simcard_id')->count());
        // $sims = Simcard::whereIn('id', $callLogs->pluck('simcard_id'))->get();
        // foreach($sims as $sim) {
        //     $this->info($sim->tel_no);
        //     $sim->generateBilling(Carbon::now());
        //     $this->info("Billing generated for simcard: " . $sim->tel_no);
        // }

        // $this->info("Billing generated for simcards: " . $sims->count());

        // $this->info(BillingItem::whereHas('billing', function($query) use ($callLogs) {
        //     $query->whereIn('simcard_id', $callLogs->pluck('simcard_id'))->whereMonth('date', '06')->whereYear('date', '2025');
        // })->count());
        // $this->info(Billing::whereIn('simcard_id', $callLogs->pluck('simcard_id'))->whereMonth('date', '06')->whereYear('date', '2025')->count());
        // Billing::whereIn('simcard_id', $callLogs->pluck('simcard_id'))->whereMonth('date', '06')->whereYear('date', '2025')->delete();
        // $this->info(Billing::whereIn('simcard_id', $callLogs->pluck('simcard_id'))->whereMonth('date', '06')->whereYear('date', '2025')->count());
        // foreach($billings as $billing) {
        //     $this->info("Simcard: $billing->simcard_id");
        //     foreach($billing->items as $item) {
        //         $this->info("Item: $item->id");
        //     }
        // }
        // $billings = Billing::where('simcard_id', $callLogs->pluck('simcard_id'))
        //         ->whereYear('date', '2025')
        //         ->whereMonth('date', '06')->get();
        // foreach ($billings as $billing) {
        //     $this->info("Simcard: $billing->simcard_id");
        //     foreach($billing->items as $item) {
        //         $this->info("Item: $item->id");
        //         $this->info("Item: $item->amount");
        //         $this->info("Item: $item->name");
        //     }
        // }

    }
}
