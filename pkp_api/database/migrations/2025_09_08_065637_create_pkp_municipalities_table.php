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
        Schema::connection('pkpulse')->create('pkp_municipality', function (Blueprint $table) {
            $table->id('municipality_id');
            $table->unsignedBigInteger('region_id');     // FK
            $table->unsignedBigInteger('province_id');   // FK
            $table->string('municipality_name', 255);
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
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pkp_municipality');
    }
};