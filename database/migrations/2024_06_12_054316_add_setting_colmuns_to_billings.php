<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('connection2')->table('billings', function (Blueprint $table) {
            $table->json('setting_options')->nullable();
        });

        Schema::connection('connection2')->table('billing_items', function (Blueprint $table) {
            $table->json('setting_options')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('connection2')->table('billings', function (Blueprint $table) {
            $table->dropColumn('setting_options');
        });

        Schema::connection('connection2')->table('billing_items', function (Blueprint $table) {
            $table->dropColumn('setting_options');
        });
    }
};
