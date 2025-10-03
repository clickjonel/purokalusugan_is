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
        Schema::create('pkp_event_resources', function (Blueprint $table) {
            $table->id('event_resource_id');
            $table->string('name');
            $table->integer('type');
            $table->decimal('beneficiary_count');
            $table->decimal('amount');
            $table->integer('event_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pkp_event_resources');
    }
};
