<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('qualtrics_forms', function (Blueprint $table) {
            $table->string('other_disease')->nullable();
            $table->string('other_followup')->nullable();
            $table->string('other_surgery')->nullable();
        });
    }

    public function down()
    {
        Schema::table('qualtrics_forms', function (Blueprint $table) {
            $table->dropColumn(['other_disease', 'other_followup', 'other_surgery']);
        });
    }
};
