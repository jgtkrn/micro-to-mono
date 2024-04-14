<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('elders', function (Blueprint $table) {
            $table->string('ccec_number')->nullable();
            $table->string('ccec_number_2')->nullable();
            $table->string('ccec_2_number')->nullable();
            $table->string('ccec_2_number_2')->nullable();
        });
    }

    public function down()
    {
        Schema::table('elders', function (Blueprint $table) {
            $table->dropColumn([
                'ccec_number',
                'ccec_number_2',
                'ccec_2_number',
                'ccec_2_number_2',
            ]);
        });
    }
};
