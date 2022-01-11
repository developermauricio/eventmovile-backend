<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEventStylesEmailTable extends Migration
{
    public function up()
    {
        Schema::table('event_styles', function (Blueprint $table) {
            $table->string('email_img_logo', 500)->nullable();
            $table->string('email_img_banner', 500)->nullable();
            $table->string('email_color_background', 100)->nullable();
            $table->string('email_btn_color', 100)->nullable();
            $table->string('email_btn_text_color', 100)->nullable();
            $table->string('email_text_color', 100)->nullable();
            $table->string('email_titles_color', 100)->nullable();
            $table->bigInteger('email_text_font')->unsigned()->nullable();
            $table->foreign('email_text_font')->references('id')->on('fonts');
            $table->bigInteger('email_titles_font')->unsigned()->nullable();
            $table->foreign('email_titles_font')->references('id')->on('fonts');
        });
    }

    public function down()
    {
        Schema::table('event_styles', function(Blueprint $table) {
            $table->dropColumn('email_img_logo');
            $table->dropColumn('email_img_banner');
            $table->dropColumn('email_color_background');
            $table->dropColumn('email_btn_color');
            $table->dropColumn('email_btn_text_color');
            $table->dropColumn('email_text_color');
            $table->dropColumn('email_titles_color');
            $table->dropColumn('email_text_font');
            $table->dropColumn('email_titles_font');
        });
    }
}
