<?php

namespace App\Models\mobile;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MerchantPlan extends Model
{
    use HasFactory;

    protected $connection = "connection2";


    public function scopeMerchantId($query, $merchant_id) {
        $query->where('merchant_id', $merchant_id);
    }
}
