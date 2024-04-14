<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('medication_histories', function (Blueprint $table) {
            $table->boolean('sign_off')->nullable();
        });
    }

    public function down()
    {
        Schema::table('medication_histories', function (Blueprint $table) {
            $table->dropColumn('sign_off');
        });
    }
};
