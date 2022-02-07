<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('integrations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email');
            $table->string('password')->nullable();
            $table->timestamps();
        });

        Schema::create('user_integrations', function (Blueprint $table) {
            $table->increments('id');
            $table->text('external_id')->index();
            $table->integer('integration_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->timestamps();

            $table->unique(['integration_id', 'user_id']);
            $table->foreign('integration_id')->references('id')
                ->on('integrations');
            $table->foreign('user_id')->references('id')
                ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_integrations');
        Schema::dropIfExists('users');
        Schema::dropIfExists('integrations');
    }
}
