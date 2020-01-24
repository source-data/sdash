<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('panel_id');
            $table->enum('type', ['file', 'url'])->default('file');
            $table->string('original_filename', 255)->nullable();
            $table->string('filename', 255)->nullable();
            $table->string('url', 255)->nullable();
            $table->string('description', 255)->nullable();
            $table->string('mime_type', 140)->nullable();
            $table->integer('version')->default(0);
            $table->boolean('is_archived')->default(false);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('panel_id')->references('id')->on('panels')->onDelete('cascade');
            $table->index('panel_id');
            $table->index('is_archived');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
}
