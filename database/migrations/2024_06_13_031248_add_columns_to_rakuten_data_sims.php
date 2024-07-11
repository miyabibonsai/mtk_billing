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
        Schema::connection('connection2')->table('rakuten_data_sims', function (Blueprint $table) {
            $table->enum('plan_type', ['monthly', 'package'])->default('package');
            $table->integer('plan_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('connection2')->table('rakuten_data_sims', function (Blueprint $table) {
            $table->dropColumn('plan_id');
            $table->dropColumn('plan_type');
        });
    }
};
