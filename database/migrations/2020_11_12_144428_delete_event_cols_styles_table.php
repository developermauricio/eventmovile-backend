<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeleteEventColsStylesTable extends Migration
{
    
    public function up()
    {
        Schema::table('events', function(Blueprint $table) {
            $table->dropColumn('pic');
            $table->dropColumn('pic_banner');
            $table->dropColumn('pic_background');
            $table->dropColumn('first_color');
            $table->dropColumn('second_color');
            $table->dropColumn('third_color');
        });
    }

    
    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('pic');
            $table->string('pic_banner');
            $table->string('pic_background');
            $table->string('first_color');
            $table->string('second_color');
            $table->string('third_color');
        });
    }
}
