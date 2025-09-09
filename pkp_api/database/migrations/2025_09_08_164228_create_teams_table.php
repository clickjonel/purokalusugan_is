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
        // Team
        Schema::connection('pkpulse')->create('pkp_team', function (Blueprint $table) {
            $table->id('team_id');
            $table->string('team_name');
            $table->timestamps();
        });

        // Team Has Scope
        Schema::connection('pkpulse')->create('pkp_team_scope', function (Blueprint $table) {
            $table->id('team_scope_id');
            $table->unsignedBigInteger('barangay_id');
            $table->unsignedBigInteger('team_id');
            $table->timestamps();
        });

        // Team Has Members
        Schema::connection('pkpulse')->create('pkp_team_member', function (Blueprint $table) {
            $table->id('team_member_id');
            $table->unsignedBigInteger('hrh_id');
            $table->unsignedBigInteger('team_id');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
