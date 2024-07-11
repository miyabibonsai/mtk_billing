<?php

namespace App\Models\mobile;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingGroup extends Model
{
    use HasFactory;

    protected $connection = "connection2";


    public function billings() {
        return $this->hasMany(Billing::class, 'billing_group_id');
    }
}
