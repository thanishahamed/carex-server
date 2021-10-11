<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('f_name');
            $table->string('l_name');
            $table->string('email')->unique();
            $table->string('telephone');
            $table->string('address');
            $table->string('status');
            $table->text('description')->nullable();
            $table->string('profile_image')->nullable();
            $table->timestamp('dob')->nullable();
            $table->string('gender');
            $table->string('nic')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('role');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
