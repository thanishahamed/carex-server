<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganDonationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organ_donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->unique()->constrained()->onDelete('cascade');
            $table->foreignId('informer_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamp('informed_on')->nullable();
            $table->string('blood_group');
            $table->text('description')->nullable();
            $table->text('informer_proof_certificate')->nullable();
            $table->boolean('agreement_accepted');
            $table->string('additional_contact');
            $table->string('status');
            $table->string('method'); //body, organ
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
        Schema::dropIfExists('organ_donations');
    }
}
