<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEventStylesBannerRegTable extends Migration
{
   
    public function up()
    {
        Schema::table('event_styles', function (Blueprint $table) {
            $table->text('section_banner_register')->nullable();
        });
    }

    public function down()
    {
        Schema::table('event_styles', function(Blueprint $table) {
            $table->dropColumn('section_banner_register');
        });
    }
}
