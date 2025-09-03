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

        Schema::connection('pkpulse')->create('pkp_indicators', function (Blueprint $table) {
            $table->id('indicator_id');
            $table->unsignedBigInteger('program_id');
            $table->string('indicator_code')->maxLength(100);
            $table->string('indicator_name');
            $table->text('indicator_description');
            $table->integer('indicator_status')->maxLength(1);
            $table->integer('indicator_scope')->maxLength(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pkp_indicators');
    }
};
