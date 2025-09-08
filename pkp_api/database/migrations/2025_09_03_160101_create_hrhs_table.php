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
        Schema::connection('pkpulse')->create('pkp_hrh_user', function (Blueprint $table) {
            $table->id('hrh_user_id');
            $table->string('user_code');
            $table->string('image')->nullable();
            $table->string('username');
            $table->string('password');
            // $table->string('prefix')->nullable();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('suffix')->nullable();
            $table->string('nickname');
            $table->string('account_status')->default('unassigned');
            $table->smallInteger('user_level');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('pkpulse')->dropIfExists('hrhs');
    }
};
