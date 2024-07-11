<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('billing.bill_unit', 30);
        $this->migrator->add('billing.sms_unit', 5);
        $this->migrator->add('billing.foreign_sms_unit', 100);
        $this->migrator->add('billing.foreign_call_unit', 300);
    }
};
