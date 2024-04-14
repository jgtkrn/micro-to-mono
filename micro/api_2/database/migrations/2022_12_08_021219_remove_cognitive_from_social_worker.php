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
        Schema::table('social_worker_forms', function (Blueprint $table) {
            $table->dropColumn([
                'forget_stuff',
                'forget_friend',
                'forget_word',
                'correct_word',
                'bad_memory',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('social_worker_forms', function (Blueprint $table) {
            // Cognitive
            $table->enum('forget_stuff', [1, 2, 3])->nullable();
            $table->enum('forget_friend', [1, 2, 3])->nullable();
            $table->enum('forget_word', [1, 2, 3])->nullable();
            $table->enum('correct_word', [1, 2, 3])->nullable();
            $table->enum('bad_memory', [1, 2, 3])->nullable();
        });
    }
};
