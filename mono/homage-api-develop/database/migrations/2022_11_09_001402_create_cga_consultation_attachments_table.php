<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cga_consultation_attachments', function (Blueprint $table) {
            $table->softDeletes();
            $table->id();
            $table->bigInteger('cga_consultation_notes_id')->unsigned();
            $table->foreign('cga_consultation_notes_id')->references('id')->on('cga_consultation_notes');

            $table->string('file_name');
            $table->string('file_path');
            $table->string('url');

            $table->json('created_by')->nullable();
            $table->json('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cga_consultation_attachments');
    }
};
