<?php

namespace App\Models\mobile;

use App\Billings\DataSimBilling;
use App\Traits\Billable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DataSim extends Model
{
    use HasFactory, Billable;

    protected $connection = "connection2";

    public function __construct()
    {
        $this->table = DB::connection($this->connection)->getDatabaseName() . '.data_sims';
    }

    public function billings() {
        return $this->morphMany(Billing::class, null, 'simcard_type', 'simcard_id');
    }

    public function generateBilling(Carbon $date) {
        $billing = new DataSimBilling($this, $date);
        return $billing->generateBilling();
    }

    public function scopeGenerateable($query) {
        $query->whereIn('ooak_status', ['using', 'instock'])->where('status', 'active');
    }
}
