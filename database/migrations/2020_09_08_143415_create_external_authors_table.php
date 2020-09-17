<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExternalAuthorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('external_authors', function (Blueprint $table) {
            $table->id();
            $table->string('firstname', 120);
            $table->string('surname', 120);
            $table->string('email');
            $table->string('institution_name', 120)->nullable();
            $table->string('department_name')->nullable();
            $table->string('orcid', 30)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('external_authors');
    }
}
