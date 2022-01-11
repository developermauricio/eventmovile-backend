<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateStickerPrintTable extends Migration
{
    public function up()
    {
        Schema::table('sticker_users', function (Blueprint $table) {
            $table->boolean('printed')->default(false);
        });
    }

    public function down()
    {
        Schema::table('sticker_users', function(Blueprint $table) {
            $table->dropColumn('printed');
        });
    }
}
