<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersPhoneTable extends Migration
{

    public function up()
    {
        Schema::table('users', function (Blueprint $table){
            $table->string('phone')->nullable();
            $table->string('lastname');
            $table->string('uid')->nullable();
            $table->string('address')->nullable();
            $table->string('specialty')->nullable();
            $table->string('gender')->nullable();
            $table->bigInteger('city_id')->nullable()->unsigned();
            $table->foreign('city_id')->references('id')->on('cities');
            $table->string('job_title')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function(Blueprint $table){
            $table->dropColumn('phone');
            $table->dropColumn('lastname');
            $table->dropColumn('uid');
            $table->dropColumn('address');
            $table->dropColumn('specialty');
            $table->dropColumn('gender');
            $table->dropColumn('city_id');
            $table->dropColumn('job_title');
            
        });
    }
}
