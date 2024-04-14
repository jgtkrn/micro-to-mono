<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('qualtrics_forms', function (Blueprint $table) {
            $table->dropColumn(['diff_rem_med']);
        });
        Schema::table('qualtrics_forms', function (Blueprint $table) {
            $table->string('diff_rem_med')->nullable();
        });
    }

    public function down()
    {
        Schema::table('qualtrics_forms', function (Blueprint $table) {
            $table->dropColumn(['diff_rem_med']);
        });
        Schema::table('qualtrics_forms', function (Blueprint $table) {
            $table->enum('diff_rem_med', [1.00, 0.75, 0.50, 0.25, 0.00, -1.00])->nullable();
        });
    }
};
