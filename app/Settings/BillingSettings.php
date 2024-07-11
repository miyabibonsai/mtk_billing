<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class BillingSettings extends Settings
{
    public int $bill_unit;
    public int $sms_unit;
    public int $foreign_sms_unit;
    public int $foreign_call_unit;
    public static function group(): string
    {
        return 'billing';
    }

}
