<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePanelAccessTokens extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('panel_access_tokens', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("token", 100);
            $table->unsignedBigInteger("panel_id");
            $table->string("qr_image_name", 100);
            $table->date("expires")->nullable();
            $table->timestamps();
            $table->index("token");
            $table->foreign('panel_id')->references('id')->on('panels');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('panel_access_tokens', function (Blueprint $table) {
            Schema::dropIfExists('panel_access_tokens');
        });
    }
}
