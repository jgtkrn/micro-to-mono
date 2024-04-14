<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('elders', function (Blueprint $table) {
            $table->unsignedBigInteger('zone_id');
            $table->string('language')->nullable();
            $table->string('centre_case_id')->nullable();
            $table->unsignedBigInteger('centre_responsible_worker_id')->nullable();
            $table->string('responsible_worker_contact')->nullable();
            $table->unsignedBigInteger('referral_id');
            $table->string('emergency_contact_number_2')->nullable();
            $table->string('emergency_contact_2_number')->nullable();
            $table->string('emergency_contact_2_number_2')->nullable();
            $table->string('emergency_contact_2_name')->nullable();
            $table->string('emergency_contact_2_relationship_other')->nullable();
        });
    }

    public function down()
    {
        Schema::table('elders', function (Blueprint $table) {
            $table->dropColumn([
                'zone_id',
                'language',
                'centre_case_id',
                'centre_responsible_worker_id',
                'responsible_worker_contact',
                'referral_id',
                'emergency_contact_number_2',
                'emergency_contact_2_number',
                'emergency_contact_2_number_2',
                'emergency_contact_2_name',
                'emergency_contact_2_relationship_other',
            ]);
        });
    }
};
