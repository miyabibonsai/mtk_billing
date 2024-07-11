<?php

namespace App\Console\Commands;

use App\Models\mobile\WaitingBillingGenerateSim;
use Exception;
use App\Traits\Billable;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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
                    ->select("$sim_table.*", "$waiting_table.id as waiting_id","$waiting_table.rewrite as rewrite", "$waiting_table.plan as waiting_plan", "$waiting_table.plan as waiting_callplan", "$waiting_table.previous_callplan as waiting_previous_callplan", "$waiting_table.date as waiting_date" )
                    ->orderBy("$waiting_table.id", 'desc')
                    ->limit(config('billings.records_per_generate'))
                    ->get();


        $waiting_ids = [];
        foreach($records as $sim) {
            $waiting_ids[] = $sim->waiting_id;
            $model_instance = new $model();
            // Array merge
            $arr = [
                $plan_column => $sim->waiting_plan ?? $sim->$plan_column,
                'callplan' => $sim->waiting_callplan ?? $sim->callplan ?? null,
                'previous_plan' => $sim->waiting_previous_plan ?? $sim->previous_plan ?? null,
                'previous_callplan' => $sim->waiting_previous_callplan ?? $sim->previous_callplan ?? null,
            ];
            // Array to model instance
            $model_instance->forceFill((array)$sim + $arr);

            // Checking Output
            $this->info("Simcard ID for $model_instance->id");
            $this->info("Plan ID for {$model_instance->$plan_column}");

            $date = new Carbon($model_instance->waiting_date);
            $model_instance->$method($date);
        }
        WaitingBillingGenerateSim::whereIn('id', $waiting_ids)->update(['status' => 'done']);
    }
}
