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
        Schema::create('pkp_indicator_values', function (Blueprint $table) {
            $table->id('indicator_value_id');
            $table->bigInteger('event_id');
            $table->bigInteger('indicator_disaggregation_id');
            $table->bigInteger('value');
            $table->bigInteger('barangay_id');
            $table->string('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pkp_indicator_values');
    }
};
