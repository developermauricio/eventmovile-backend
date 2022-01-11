<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEventEmailSubjectTable extends Migration
{
    
    public function up()
    {
        Schema::table('events', function ($table) {
            $table->string('subject_email', 250)->nullable();
        });
    }

   
    public function down()
    {
        Schema::table('events', function(Blueprint $table) {
            $table->dropColumn('subject_email');
        });
    }
}
