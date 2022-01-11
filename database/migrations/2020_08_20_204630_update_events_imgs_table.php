<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEventsImgsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('pic_banner');
            $table->string('pic_background');
            $table->string('first_color');
            $table->string('second_color');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events', function(Blueprint $table) {
            $table->dropColumn('pic_banner');
            $table->dropColumn('pic_background');
            $table->dropColumn('first_color');
            $table->dropColumn('second_color');
        });
    }
}
