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
        Schema::table('cga_consultation_signs', function (Blueprint $table) {
            $table->string('signature_name')->nullable();
            $table->string('signature_remark')->nullable();
        });
        Schema::table('bzn_consultation_signs', function (Blueprint $table) {
            $table->string('signature_name')->nullable();
            $table->string('signature_remark')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cga_consultation_signs', function (Blueprint $table) {
            $table->dropColumn([
                'signature_name',
                'signature_remark'
            ]);
        });
        Schema::table('bzn_consultation_signs', function (Blueprint $table) {
            $table->dropColumn([
                'signature_name',
                'signature_remark'
            ]);
        });
    }
};
