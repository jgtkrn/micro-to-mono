<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('elders', function (Blueprint $table) {
            $table->renameColumn('UID', 'uid');
            $table->renameColumn('UID_connected_with', 'uid_connected_with');
        });
    }

    public function down()
    {
        Schema::table('elders', function (Blueprint $table) {
            $table->renameColumn('uid', 'UID');
            $table->renameColumn('uid_connected_with', 'UID_connected_with');
        });
    }
};
