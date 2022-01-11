<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateBmPasswordTable extends Migration
{
    public function up()
    {
        Schema::table('business_markets', function (Blueprint $table) {
            $table->string('password')->nullable();
        });
    }

    public function down()
    {
        Schema::table('business_markets', function(Blueprint $table) {
            $table->dropColumn('password');
        });
    }
}
