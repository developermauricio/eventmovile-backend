<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateHallsPicBannerTable extends Migration
{
    
    public function up()
    {
        {
            Schema::table('halls', function (Blueprint $table) {
                $table->string('pic_banner', 300);
            });
        }
    
    }
    public function down()
    {
        Schema::table('halls', function(Blueprint $table) {
            $table->dropColumn('pic_banner');
        });
    }
}
