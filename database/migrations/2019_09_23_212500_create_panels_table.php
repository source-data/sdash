<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePanelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('panels', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->text('caption')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->string('type', 100);
            $table->string('subtype', 100);
            $table->mediumInteger('clicks')->default(0);
            $table->mediumInteger('downloads')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->timestamp('made_public_at')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('user_id');



        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('panels');
    }
}
