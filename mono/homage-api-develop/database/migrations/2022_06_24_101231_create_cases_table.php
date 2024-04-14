<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cases', function (Blueprint $table) {
            $table->id();
            $table->string('case_name', 20)->nullable();
            $table->integer('case_number');
            $table->string('case_status')->nullable();
            $table->foreignId('elder_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->longText('updated_by')->nullable();
            $table->longText('created_by')->nullable();

        });
    }

    public function down()
    {
        Schema::dropIfExists('cases');
    }
};
