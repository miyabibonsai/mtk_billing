<?php

namespace App\Console\Commands;

use App\Models\mobile\DataSim;
use App\Models\mobile\WaitingBillingGenerateSim;
use Exception;
use App\Traits\Billable;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GenerateBilling extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate-billing {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Billing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if(!array_key_exists( $this->argument('type'), config('billings.types'))) {
            throw new Exception("Type is not in the list");
        }

        $model = config('billings.types')[$this->argument('type')];
        if(!in_array(Billable::class, class_uses($model), true)) {
            throw new Exception("Billable trait does not exist in $model");
        }

        $instance = app($model);
        $method = 'generateBilling';
        if(!method_exists($instance, $method)) {
            throw new Exception("$method method does not exist in $model");
        }

        /**Get Tables and Simcards */
        $sim_table = $instance->getTable();
        $waiting_table = (new WaitingBillingGenerateSim())->getTable();
        $plan_column = $instance->getPlanColumn();

        /**Get Records from Simcards and Waiting Sims */
        $records = DB::table($sim_table)
                    ->join($waiting_table, "$waiting_table.sim_id", '=', "$sim_table.id")
                    ->where("$waiting_table.status", 'waiting')
                    ->where("$waiting_table.simcard_type", $model)
                    ->whereNot("$waiting_table.plan", 0)
                    ->whereIn("$sim_table.mc", [0,20])
                    ->select("$sim_table.*", "$waiting_table.id as waiting_id","$waiting_table.rewrite as rewrite", "$waiting_table.plan as waiting_plan", "$waiting_table.callplan as waiting_callplan", "$waiting_table.previous_callplan as waiting_previous_callplan", "$waiting_table.date as waiting_date" )
                    ->orderBy("$waiting_table.id", 'desc')
                    ->limit(config('billings.records_per_generate'))
                    ->get();

        $this->info(count($records));
        $i = 0;
        foreach($records as $sim) {
            $model_instance = new $model();
            // Array merge
            $arr = [
                $plan_column => $sim->waiting_plan ?? $sim->$plan_column,
                'callplan' => $sim->waiting_callplan ?? $sim->callplan ?? null,
                'previous_plan' => $sim->waiting_previous_plan ?? $sim->previous_plan ?? null,
                'previous_callplan' => $sim->waiting_previous_callplan ?? $sim->previous_callplan ?? null,
            ];
            $i++;


            $model_instance->forceFill($arr + (array)$sim);

            // // Checking Output
            $this->info("Simcard ID for $model_instance->id");
            $this->info("Plan ID for {$model_instance->$plan_column}");

            $date = new Carbon($model_instance->waiting_date);
            $billing = $model_instance->$method($date);
            $waiting = WaitingBillingGenerateSim::find($sim->waiting_id);
            if($waiting) {
                $waiting->update([
                    'status' => 'done',
                ]);
                if($waiting->request_id) {
                    DB::connection('connection2')->table('requests')->where('id', $waiting->request_id)->update([
                        "billing_id" => $billing->id
                    ]);
                }
            }

        }
        $this->info($i);

    }
}
