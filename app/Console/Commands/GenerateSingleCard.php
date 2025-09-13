<?php

namespace App\Console\Commands;

use Exception;
use App\Traits\Billable;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateSingleCard extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate-single-card {type} {id} {date?}';

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
        if(!array_key_exists( $this->argument('type'), config('billings.types'))) {
            throw new Exception("Type is not in the list");
        }

        $model = config('billings.types')[$this->argument('type')];
        if(!in_array(Billable::class, class_uses($model), true)) {
            throw new Exception("Billable trait does not exist in $model");
        }
        $date = new Carbon($this->argument('date'));
        $sim = app($model)->find($this->argument('id'));
        $this->info($date);
        $this->info($sim);
        $sim->generateBilling($date);
        $this->info("Done");
        return 0;
    }
}
