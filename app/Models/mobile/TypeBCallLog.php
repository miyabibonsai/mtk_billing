<?php

namespace App\Models\mobile;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeBCallLog extends Model
{
    use HasFactory;
    protected $connection = 'connection2';
    protected $table = 'simcard_b_call_logs';

    public function simcard_b()
    {
        return $this->belongsTo(SimcardB::class, 'simcard_id');
    }
}
