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
            $table->integer('event_type');
            $table->date('event_date');
            $table->string('event_venue');
            $table->decimal('event_budget');
            $table->decimal('event_actual_budget');
            $table->string('event_fund_source');
            $table->string('event_proponent');
            $table->string('event_partner');
            $table->string('event_scope');
            $table->boolean('is_pk_site');            
            $table->timestamps();
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
