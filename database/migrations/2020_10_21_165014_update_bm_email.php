<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateBmEmail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('business_markets', function (Blueprint $table) {
            $table->string('subject_email')->nullable();
            $table->string('message_email')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('business_markets', function(Blueprint $table) {
            $table->dropColumn('subject_email');
            $table->dropColumn('message_email');
        });
    }
}
