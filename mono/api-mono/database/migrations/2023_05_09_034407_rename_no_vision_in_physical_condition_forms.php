<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('physical_condition_forms', function (Blueprint $table) {
            $table->renameColumn('no_vision', 'non_verbal');
        });
    }

    public function down()
    {
        Schema::table('physical_condition_forms', function (Blueprint $table) {
            $table->renameColumn('non_verbal', 'no_vision');
        });
    }
};
