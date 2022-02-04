<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUploadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tag')->unique();
            $table->timestamps();
        });

        Schema::create('uploads', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_integration_id');
            $table->boolean('attached')->default(false);
            $table->text('url');
            $table->timestamp('indexed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_integration_id')
                ->references('id')
                ->on('user_integrations');
        });

        Schema::create('tag_upload', function (Blueprint $table) {
            $table->unsignedInteger('tag_id');
            $table->unsignedInteger('upload_id');

            $table->foreign('tag_id')
                ->references('id')
                ->on('tags');
            $table->foreign('upload_id')
                ->references('id')
                ->on('uploads');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tag_upload');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('uploads');
    }
}
