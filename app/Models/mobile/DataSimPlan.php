<?php

namespace App\Models\mobile;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DataSimPlan extends Model
{
    use HasFactory;

    protected $connection = "connection2";

    public function __construct()
    {
        $this->table = DB::connection($this->connection)->getDatabaseName() . '.data_sim_plans';
    }
}
