<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("files", function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string("file_name");
            $table->string("disk_name");
            $table
                ->bigInteger("user_id")
                ->unsigned()
                ->nullable();
            $table
                ->bigInteger("event_id")
                ->unsigned()
                ->nullable();
            $table
                ->foreign("event_id")
                ->references("id")
                ->on("events");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("files");
    }
};
