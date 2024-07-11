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
        Schema::connection('connection2')->create('data_sim_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('gb');
            $table->float('mb');
            $table->float('price');
            $table->enum('type', ['pocket-wifi', 'datasim']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('connection2')->dropIfExists('data_sim_plans');
    }
};
