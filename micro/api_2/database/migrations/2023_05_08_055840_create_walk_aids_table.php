<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('qualtrics_forms', function (Blueprint $table) {
            $table->dropColumn([
                'walk_aid'
            ]);
        });
        Schema::create('walk_aids', function (Blueprint $table) {
            $table->softDeletes();
            $table->id();
            $table->bigInteger('qualtrics_form_id')->unsigned();
            $table->foreign('qualtrics_form_id')->references('id')->on('qualtrics_forms');
            $table->smallInteger('walk_aid')->unsigned()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('qualtrics_forms', function (Blueprint $table) {
            $table->enum('walk_aid', [1, 2, 3, 4, 5, 6])->nullable();
        });
        Schema::dropIfExists('walk_aids');
    }
};
