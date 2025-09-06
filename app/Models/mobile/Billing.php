<?php

namespace App\Models\mobile;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
    use HasFactory;

    protected $connection = "connection2";

    protected $casts = [
        'setting_options' => 'array',
    ];

    public function scopeDatasim($query) {
        $query->where('simcard_type', DataSim::class);
    }

    public function simmable() {
        return $this->morphTo(type: 'simcard_type', id: 'simcard_id');
    }

    public function items() {
        return $this->hasMany(BillingItem::class, 'billing_id', 'id');
    }
}
