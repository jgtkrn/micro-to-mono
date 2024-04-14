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
        Schema::table('elders', function (Blueprint $table) {
            $table->string('second_contact_number')->nullable();
            $table->string('third_contact_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('elders', function (Blueprint $table) {
            $table->dropColumn([
                'second_contact_number', 
                'third_contact_number'
            ]);
        });
    }
};
