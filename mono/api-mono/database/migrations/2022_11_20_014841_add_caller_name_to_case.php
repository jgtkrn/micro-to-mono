<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('cases', function (Blueprint $table) {
            $table->string('caller_name')->nullable();
        });
    }

    public function down()
    {
        Schema::table('cases', function (Blueprint $table) {
            $table->dropColumn([
                'caller_name',
            ]);
        });
    }
};
