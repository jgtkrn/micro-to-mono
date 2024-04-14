<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cases', function (Blueprint $table) {
            $table->dropColumn('case_elder_remark');
        });
        Schema::table('elders', function (Blueprint $table) {
            $table->string('elder_remark')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cases', function (Blueprint $table) {
            $table->string('case_elder_remark')->nullable();
        });
        Schema::table('elders', function (Blueprint $table) {
            $table->dropColumn('elder_remark');
        });
    }
};
