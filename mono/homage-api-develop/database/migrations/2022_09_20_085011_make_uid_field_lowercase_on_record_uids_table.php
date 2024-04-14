<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('record_uids', function (Blueprint $table) {
            $table->renameColumn('UID', 'uid');
        });
    }

    public function down()
    {
        Schema::table('record_uids', function (Blueprint $table) {
            $table->renameColumn('uid', 'UID');
        });
    }
};
