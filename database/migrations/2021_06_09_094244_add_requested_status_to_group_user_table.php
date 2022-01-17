<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddRequestedStatusToGroupUserTable extends Migration
{

    protected $tableName = 'group_user';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE " . $this->tableName . " MODIFY COLUMN status ENUM('confirmed', 'pending', 'requested') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE " . $this->tableName . " MODIFY COLUMN status ENUM('confirmed', 'pending') NOT NULL DEFAULT 'pending'");
    }
}
