<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFigureToPanelPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('figure_panel', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('figure_id');
            $table->unsignedBigInteger('panel_id');
            $table->integer('panel_order')->nullable();
            $table->timestamps();

            $table->foreign('figure_id')->references('id')->on('figures');
            $table->index('figure_id');
            $table->foreign('panel_id')->references('id')->on('panels');
            $table->index('panel_id');
            $table->index('panel_order');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('figure_panel');
    }
}
