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
            $table->timestamps();
        });
        Schema::connection('pkpulse')->alter('pkp_indicators', function (Blueprint $table) {
            $table->bigInteger('program_id')->unsigned()->after('indicator_id');
            $table->string('indicator_code')->maxLength(100)->after('program_id');
            $table->string('indicator_name')->after('indicator_code');
            $table->text('indicator_description')->after('indicator_name');
            $table->integer('indicator_status')->maxLength(1)->after('indicator_description');
            $table->integer('indicator_scope')->maxLength(1)->after('indicator_status');
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
