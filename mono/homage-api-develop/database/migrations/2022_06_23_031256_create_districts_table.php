<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->string('district_name', 20)->unique();
            $table->string('bzn_code');
            $table->softDeletes();
            $table->timestamps();
            $table->longText('created_by')->nullable();
            $table->longText('updated_by')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('districts');
    }
};
