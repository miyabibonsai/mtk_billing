<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\mobile\Billing;
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
        $billings = Billing::datasim()
                ->where('amount', 0)
                ->where('date', '>', '2024-09-01')
                ->with('simmable')
                ->count();
        $this->info($billings);
        return 0;
        $date = new Carbon();
        foreach($billings as $billing) {
            Log::info($billing);
            try{
                DB::beginTransaction();
                $sim = $billing->simmable;
                $sim->generateBilling(clone $date);
            } catch (Exception $e) {
                DB::rollBack();
                throw $e;
                break;
            }
            DB::commit();
            }
        }
}
