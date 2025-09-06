<?php
namespace App\Models\mobile; // Changed from App\Models

use App\Billings\SimcardBBilling;
use App\Traits\Billable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Rennokki\QueryCache\Traits\QueryCacheable;

class SimcardB extends Model
{
    use HasFactory, Billable;

    protected $connection = "connection2";
    protected $merchant_column = 'merchant';
    protected $plan_column = 'plan';


    protected $table = 'simcard_type_b';

    public function __construct()
    {
        $this->table = DB::connection($this->connection)->getDatabaseName() . '.simcard_type_b';
    }

    // Define fillable properties if you use mass assignment (create, update)
    protected $fillable = [
        'status',
        'ooak_status',
        'target_status',
        'kpo',
        'kpg',
        'tel_no',
        'iccid',
        'sim_status',
        'foreign_call',
        'foreign_deposit',
        'plan', // Assuming this is the FK for mobile_plan
        'simcard_plan_id', // Assuming this is the FK for simcard_plan
        'callplan',
        'previous_plan',
        'previous_callplan',
        'imsi',
        'start_date',
        'activation_date',
        'deactivation_date',
        'end_service',
        'user_type',
        'foreign_call_bill',
        'email',
        'user_id', // FK for user
        'mc',
        'merchant', // FK for dairiten
        // Add any other fields that can be mass-assigned
    ];

    /**
     * Get the user associated with the SimcardB.
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function generateBilling(Carbon $date) {
        $billing = new SimcardBBilling($this, $date);
        return $billing->generateBilling();
    }
}

