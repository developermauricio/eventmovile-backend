<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumToEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->boolean('req_web_app')->default(0);
            $table->boolean('wa_req_path')->default(0);
            $table->boolean('wa_req_mapa')->default(false);
            $table->boolean('wa_req_feria_comercial')->default(0);
            $table->string('wa_path_value')->nullable();
            $table->string('wa_mapa_value')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('req_web_app');
            $table->dropColumn('wa_req_path');
            $table->dropColumn('wa_req_mapa');
            $table->dropColumn('wa_req_feria_comercial');
            $table->dropColumn('wa_path_value');
            $table->dropColumn('wa_mapa_value');
        });
    }
}
