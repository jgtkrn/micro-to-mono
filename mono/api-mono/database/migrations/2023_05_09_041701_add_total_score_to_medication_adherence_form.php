<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('medication_adherence_forms', function (Blueprint $table) {
            $table->decimal('total_mmas_score')->nullable();
        });
    }

    public function down()
    {
        Schema::table('medication_adherence_forms', function (Blueprint $table) {
            $table->dropColumn('total_mmas_score');
        });
    }
};
