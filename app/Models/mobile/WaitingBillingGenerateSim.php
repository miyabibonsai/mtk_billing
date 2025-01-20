<?php

namespace App\Models\mobile;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class WaitingBillingGenerateSim extends Model
{
    use HasFactory;

    protected $connection = "connection2";
    protected $guarded = [];

    public function __construct()
    {
        $this->table = DB::connection($this->connection)->getDatabaseName() . '.waiting_billing_generate_sims';
    }
}
