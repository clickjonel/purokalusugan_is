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
        Schema::create('pkp_indicator_disaggregations', function (Blueprint $table) {
            $table->id('indicator_disaggregation_id'); 
            $table->bigInteger('indicator_id'); 
            $table->bigInteger('disaggration_id'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pkp_indicator_disaggregations');
    }
};
