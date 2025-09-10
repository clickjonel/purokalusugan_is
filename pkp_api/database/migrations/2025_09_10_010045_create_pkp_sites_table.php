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
        Schema::connection('pkpulse')->create('pkp_site', function (Blueprint $table) {
            $table->id('site_id');
            $table->unsignedBigInteger('barangay_id');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->integer('site_status')->default(1);
            $table->integer('no_purok')->nullable();
            $table->integer('no_sitio')->nullable();
            $table->integer('target_purok')->nullable();
            $table->integer('target_sitio')->nullable();
            $table->integer('no_household')->nullable();
            $table->integer('population')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pkp_site');
    }
};
