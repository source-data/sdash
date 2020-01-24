<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->text('comment');
            $table->unsignedBigInteger('reply_to')->nullable();
            $table->unsignedBigInteger('panel_id')->nullable();
            $table->unsignedBigInteger('group_id')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');            
            $table->foreign('panel_id')->references('id')->on('panels');            
            $table->foreign('group_id')->references('id')->on('groups');      
            $table->index('user_id');      
            $table->index('panel_id');      
            $table->index('group_id');      
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments');
    }
}
