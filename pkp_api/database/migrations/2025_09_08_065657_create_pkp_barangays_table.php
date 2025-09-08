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
        Schema::connection('pkpulse')->create('pkp_barangay', function (Blueprint $table) {
            $table->id('barangay_id');
            $table->unsignedBigInteger('region_id');         // FK
            $table->unsignedBigInteger('province_id');       // FK
            $table->unsignedBigInteger('municipality_id');   // FK
            $table->string('barangay_name', 255);
            $table->unsignedBigInteger('uid');
            $table->timestamps();
            // $table->softDeletes();

            // $table->foreign('region_id')
            //     ->references('region_id')
            //     ->on('pkp_region')
            //     ->onDelete('cascade');

            // $table->foreign('province_id')
            //     ->references('province_id')
            //     ->on('pkp_province')
            //     ->onDelete('cascade');

            // $table->foreign('municipality_id')
            //     ->references('municipality_id')
            //     ->on('pkp_municipality')
            //     ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pkpulse')->dropIfExists('pkp_barangay');
    }
};