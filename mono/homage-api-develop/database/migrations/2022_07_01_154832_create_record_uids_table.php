<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('record_uids', function (Blueprint $table) {
            $table->id();
            $table->string('UID')->unique();
            $table->softDeletes();
            $table->longText('created_by')->nullable();
            $table->longText('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('record_uids');
    }
};
