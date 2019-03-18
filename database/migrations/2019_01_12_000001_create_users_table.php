<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\User;

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
            $table->increments('id_user');
            // $table->unsignedInteger('id_group')->default(User::REGULAR_USER);
            $table->string('name', 150);
            $table->string('email', 255)->unique();
            $table->string('gender', 6)->nullable();
            $table->date('birthdate')->nullable();
            $table->string('nationality', 150)->nullable();
            $table->string('city', 150)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('postcode', 20)->nullable();
            $table->string('phone', 21);
            $table->string('password', 300);
            $table->string('pp', 255)->nullable();
            $table->integer('blocked')->default(0);
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('id_group')->references('id_group')->on('user_groups');
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
