<?php

namespace App\Models\mobile;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingItem extends Model
{
    use HasFactory;

    protected $connection = "connection2";

    protected $casts = [
        'setting_options' => 'array',
    ];
}
