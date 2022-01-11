<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEventStylesTable extends Migration
{
    
    public function up()
    {
        Schema::table('event_styles', function (Blueprint $table) {
            $table->string('link_facebook', 500)->nullable();
            $table->string('link_instagram', 500)->nullable();
            $table->string('link_twitter', 500)->nullable();
        });
    }

   
    public function down()
    {
        Schema::table('event_styles', function(Blueprint $table) {
            $table->dropColumn('link_facebook');
            $table->dropColumn('link_instagram');
            $table->dropColumn('link_twitter');

        });
    }
}
