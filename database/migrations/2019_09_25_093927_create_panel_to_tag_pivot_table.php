<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePanelToTagPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('panel_tag', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('panel_id');
            $table->unsignedBigInteger('tag_id');
            $table->enum('origin', ['smarttag','user']);
            $table->string('role', 60)->nullable();
            $table->string('type',40)->nullable();
            $table->string('category',40)->nullable();
            $table->timestamps();

            $table->foreign('tag_id')->references('id')->on('tags');
            $table->index('tag_id');
            $table->foreign('panel_id')->references('id')->on('panels');
            $table->index('panel_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('panel_tag');
    }
}
