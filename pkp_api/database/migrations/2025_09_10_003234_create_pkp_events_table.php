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
        Schema::create('pkp_events', function (Blueprint $table) {
            $table->id('event_id');
            $table->string('event_name');
            $table->integer('event_type');
            $table->date('event_date');
            $table->string('event_venue');
            $table->decimal('event_budget');
            $table->decimal('event_actual_budget');
            $table->string('event_fund_source');
            $table->string('event_proponents');
            $table->string('event_partners');           
            $table->timestamps();
        });

        Schema::create('pkp_event_programs', function (Blueprint $table) {
            $table->id('event_program_id');
            $table->integer('event_id');
            $table->integer('program_id');
        });

        Schema::create('pkp_event_barangays', function (Blueprint $table) {
            $table->id('event_barangay_id');
            $table->integer('barangay_id');
            $table->integer('event_id');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pkp_events');
    }
};
