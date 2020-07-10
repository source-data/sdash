<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFileSizeAndIsArchivedToImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('images', function (Blueprint $table) {
            $table->integer('file_size')->unsigned()->nullable()->after('preview_filename');
            $table->boolean('is_archived')->default(false)->after('file_size');
            $table->softDeletes();
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
        Schema::table('images', function (Blueprint $table) {
            $table->dropColumn('file_size');
            $table->dropColumn('is_archived');
            $table->dropSoftDeletes();
        });
    }
}
