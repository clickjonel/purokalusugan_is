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
        Schema::connection('pkpulse')->create('pkp_province', function (Blueprint $table) {
            $table->id('province_id');
            $table->unsignedBigInteger('uidregion_id');
            $table->text('province_name');
            $table->unsignedBigInteger('uid');
            $table->timestamps();

            $table->foreign('region_id')
                ->references('region_id')
                ->on('pkp_region')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pkp_province');
    }
};