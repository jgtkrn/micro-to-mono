<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('elder_calls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cases_id')->constrained()->cascadeOnDelete();
            $table->integer('caller_id')->nullable();
            $table->dateTime('call_start')->nullable();
            $table->dateTime('call_end')->nullable();
            $table->string('call_status', 250)->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->longText('created_by')->nullable();
            $table->longText('updated_by')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('elder_calls');
    }
};
