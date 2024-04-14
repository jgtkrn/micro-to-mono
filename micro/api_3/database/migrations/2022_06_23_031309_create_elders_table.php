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
        Schema::create('elders', function (Blueprint $table) {
            $table->id();
            $table->string('UID')->unique()->index();
            $table->string('name',120);
            $table->enum('gender',['male','female']);
            $table->integer('birth_day');
            $table->integer('birth_month');
            $table->string('birth_year',4);
            $table->string('contact_number',13)->nullable();
            $table->text('address');
            $table->foreignId('district_id')->constrained();
            $table->string('emergency_contact_name',120)->nullable();
            $table->string('emergency_contact_number',13)->nullable();
            $table->text('emergency_contact_relationship_other',13)->nullable();
            $table->string('relationship',12)->nullable();
            $table->string('UID_connected_with',12)->nullable();
            $table->text('health_issue')->nullable();
            $table->text('medication')->nullable();
            $table->string('limited_mobility',15)->nullable();
            $table->enum('case_type',['CGA','BZN']);
            $table->softDeletes();
            $table->timestamps();
            $table->longText('created_by')->nullable();
            $table->longText('updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('elders');
    }
};
