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
        $counts = DataSim::whereHas('billings', function($query) {
            // $this->info(Carbon::now()->month());
            // $this->info(Carbon::now()->year());
            $query->whereMonth('date', "01")->whereYear('date', "2025");
        })->count();
        $mustCounts = DataSim::generateable()->count();
        $this->info($counts);
        $this->info($mustCounts);
    }
}
