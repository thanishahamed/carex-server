<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScholarshipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scholarships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            $table->string('target'); // to whome (A/L, O/L, Degree Progames)
            $table->string('subject');
            $table->integer('no_of_scholarships');
            $table->biginteger('worth_of_scholarship'); //total wealth
            $table->text('description');
            $table->boolean('agreement_accepted');
            $table->string('status');
            $table->string('additional_contact');
            $table->boolean('hide_identity');
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
        Schema::dropIfExists('scholarships');
    }
}
