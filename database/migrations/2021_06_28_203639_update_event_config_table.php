<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEventConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events', function (Blueprint $table){
            $table->boolean('req_chat')->nullable()->default(true);
            $table->boolean('req_make_question')->nullable()->default(true);
            $table->boolean('req_files')->nullable()->default(true);
            $table->boolean('req_schedule')->nullable()->default(true);
            $table->boolean('req_probes')->nullable()->default(true);
            $table->boolean('req_survey')->nullable()->default(true);
            $table->boolean('req_networking')->nullable()->default(true);
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
            $table->dropColumn('req_chat');
            $table->dropColumn('req_make_question');
            $table->dropColumn('req_files');
            $table->dropColumn('req_schedule');
            $table->dropColumn('req_probes');
            $table->dropColumn('req_survey');
            $table->dropColumn('req_networking');
        });
    }
}
