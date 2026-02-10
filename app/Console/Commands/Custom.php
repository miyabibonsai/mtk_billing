<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\mobile\Billing;
use App\Models\mobile\BillingGroup;
use App\Models\mobile\BillingItem;
use App\Models\mobile\CallLog;
use App\Models\mobile\DataSim;
use App\Models\mobile\Simcard;
use App\Models\mobile\SimcardB;
use App\Models\mobile\TypeBCallLog;
use App\Models\mobile\WaitingBillingGenerateSim;
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

        // $count = SimcardB::whereNotNull('previous_callplan')->where('previous_callplan', '!=', '0')->whereHas('billings', function($query) {
        //     $query->whereMonth('date', '01')->whereYear('date', '2026');
        // })->get();
        // $billing = Billing::where('simcard_type', 'App\Models\mobile\Simcard')
        //     ->whereMonth('date', '01')
        //     ->whereYear('date', '2026')
        //     ->where('simcard_id', 142)->first();
        // $this->info("Total Amount: " . $billing->amount);
        // $billingItems = BillingItem::where('billing_id', $billing->id)->get();
        // foreach ($billingItems as $item) {
        //     $this->info("Billing Item ID: {$item->id} Amount: {$item->price} Description: {$item->description}");
        // }
        // $typeBCallLogs = TypeBCallLog::where('simcard_id', $billing->simcard_id)
        //     ->whereMonth('date', '12')
        //     ->whereYear('date', '2025')
        //     ->get();
        // foreach ($typeBCallLogs as $log) {
        //     $this->info("Type B Call Log ID: {$log->id} Date: {$log->date} Duration: {$log->duration} Bill Unit 60: {$log->billunit_60}");
        // }
        BillingGroup::whereDate('date', '>=','2025-12-31')->delete();
        Billing::whereDate('date', '>=','2025-12-31')->delete();
        BillingItem::whereHas('billing', function($query) {
            $query->whereDate('date', '>=','2025-12-31');
        })->delete();
        // $this->info("Total Count: " . $count->count());

        // TypeBCallLog::whereDate('date', '>=','2025-12-31')->delete();

        // $lastMonth = Carbon::now()->subMonth();
        // $callLogs = TypeBCallLog::whereHas('simcard_b', function($query) {
        //     $query->whereNotNull('simcard_b.previous_callplan')->where('simcard_b.previous_callplan', '!=', '0');
        // })->whereMonth('date', $lastMonth->month)->with('simcard_b')->whereYear('date', $lastMonth->year)->get();
        // foreach ($callLogs as $log) {
        //     $this->info("Gettings call logs ID {$log->id} simcard ID {$log->simcard_id} from callplan {$log->simcard_b->callplan} to previous callplan {$log->simcard_b->previous_callplan}");
        //     $this->info('Duration Ori: ' . $log->duration_ori );
        //     $this->info('Duration: ' . $log->duration . ' unit');
        //     $this->info('Bill Unit 60: ' . $log->billunit_60 . ' unit');
        // }
        // $this->info($callLogs);
    }
}
