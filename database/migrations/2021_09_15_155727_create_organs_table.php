<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrgansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organ_donation_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('alive_death');
            $table->string('organ_name'); //donation info body, all, heart, kidney
            $table->string('transplanted_to')->nullable();
            $table->string('received_address')->nullable();
            $table->string('status');
            $table->integer('received_age')->nullable();
            $table->timestamp('received_on')->nullable();
            $table->string('received_nic')->nullable();
            $table->text('description')->nullable();
            $table->boolean('agreement_accepted');
            $table->string('additional_contact');
            $table->text('agreement_link');
            $table->text('hospital_certificate_link');
            $table->boolean('hide_identity');
            $table->boolean('additional_tests');
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
        Schema::dropIfExists('organs');
    }
}
