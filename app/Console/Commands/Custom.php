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


    }
}
