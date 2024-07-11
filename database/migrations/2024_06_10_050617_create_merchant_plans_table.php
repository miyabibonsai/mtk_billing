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
        Schema::connection('connection2')->create('merchant_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('merchant_id');
            $table->morphs('plannable');
            $table->float('price');
            $table->unsignedBigInteger('photo_id');
            $table->text('description')->nullable();
            $table->float('register_fee')->default(0);
            $table->text('register_fee_explain')->nullable();
            $table->json('options')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('connection2')->dropIfExists('merchant_plans');
    }
};
