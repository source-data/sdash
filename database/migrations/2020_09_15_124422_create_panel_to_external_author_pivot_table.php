<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePanelToExternalAuthorPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('external_author_panel', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('external_author_id');
            $table->unsignedBigInteger('panel_id');
            $table->enum('role', ['author', 'corresponding', 'curator'])->default('author');
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->foreign('panel_id')->references('id')->on('panels')->onDelete('cascade');
            $table->index('panel_id');
            $table->foreign('external_author_id')->references('id')->on('external_authors')->onDelete('cascade');
            $table->index('external_author_id');
            $table->index('order');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('external_author_panel');
    }
}
