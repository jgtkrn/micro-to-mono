<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('social_worker_forms', function (Blueprint $table) {
            $table->smallInteger('social_5')->unsigned()->nullable();
        });
    }

    public function down()
    {
        Schema::table('social_worker_forms', function (Blueprint $table) {
            $table->dropColumn('social_5');
        });
    }
};
