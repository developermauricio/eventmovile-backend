<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeleteActivitiesColsStylesTable extends Migration
{
    
    public function up()
    {
        Schema::table('activities', function(Blueprint $table) {
            $table->dropColumn('pic');
            $table->dropColumn('pic_banner');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->string('pic');
            $table->string('pic_banner');
        });
    }
}
