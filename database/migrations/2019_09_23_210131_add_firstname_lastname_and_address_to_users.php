<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFirstnameLastnameAndAddressToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->string('firstname', 120);
            $table->string('surname', 120);
            $table->enum('role', ['user', 'admin', 'superadmin'])->default('user');
            $table->string('institution_name', 120)->nullable();
            $table->string('institution_address')->nullable();
            $table->string('department_name')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('twitter')->nullable();
            $table->string('orcid', 30)->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name');
            $table->dropColumn('firstname');
            $table->dropColumn('surname');
            $table->dropColumn('role');
            $table->dropColumn('institution_name');
            $table->dropColumn('institution_address');
            $table->dropColumn('department_name');
            $table->dropColumn('linkedin');
            $table->dropColumn('twitter');
            $table->dropColumn('orcid');
        });
    }
}
