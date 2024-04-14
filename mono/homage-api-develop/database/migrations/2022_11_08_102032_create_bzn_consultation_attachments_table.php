<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bzn_consultation_attachments', function (Blueprint $table) {
            $table->softDeletes();
            $table->id();
            $table->bigInteger('bzn_consultation_notes_id')->unsigned();
            $table->foreign('bzn_consultation_notes_id')->references('id')->on('bzn_consultation_notes');

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
        Schema::dropIfExists('bzn_consultation_attachments');
    }
};
