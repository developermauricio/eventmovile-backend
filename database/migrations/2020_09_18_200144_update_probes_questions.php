<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProbesQuestions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('probes_questions', function ($table) {
            $table->dropColumn('required_probe');
            $table->dropColumn('probe_id');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('probes_questions', function ($table) {
            $table->boolean('required_probe')->default(false);
        });
    }
}
