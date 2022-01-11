<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventStylesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_styles', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('event_id')->unsigned()->unique();
            $table->foreign('event_id')->references('id')->on('events');
            //home
            $table->string('home_img_background', 500)->nullable();
            $table->string('home_img_banner', 500)->nullable();
            $table->string('home_img_logo', 500)->nullable();
            $table->string('home_color_background', 100)->nullable();
            $table->string('home_titles_color', 100)->nullable();
            $table->string('home_text_color', 100)->nullable();

            $table->bigInteger('home_titles_font')->unsigned()->nullable();
            $table->foreign('home_titles_font')->references('id')->on('fonts');

            $table->bigInteger('home_text_font')->unsigned()->nullable();
            $table->foreign('home_text_font')->references('id')->on('fonts');

            $table->string('home_btn_color', 100)->nullable();
            $table->string('home_btn_color_hover', 100)->nullable();
            $table->string('home_btn_text_color', 100)->nullable();
            $table->string('home_btn_text_color_hover', 100)->nullable();
            $table->string('home_footer_color', 100)->nullable();
            $table->string('home_div_first_color', 100)->nullable();
            $table->string('home_div_second_color', 100)->nullable();
            //section
            $table->string('section_img_background', 500)->nullable();
            $table->string('section_color_background', 100)->nullable();
            $table->string('section_titles_color', 100)->nullable();
            $table->string('section_text_color', 100)->nullable();

            $table->bigInteger('section_titles_font')->unsigned()->nullable();
            $table->foreign('section_titles_font')->references('id')->on('fonts');

            $table->bigInteger('section_text_font')->unsigned()->nullable();
            $table->foreign('section_text_font')->references('id')->on('fonts');

            $table->string('section_btn_color', 100)->nullable();
            $table->string('section_btn_color_hover', 100)->nullable();
            $table->string('section_btn_text_color', 100)->nullable();
            $table->string('section_btn_text_color_hover', 100)->nullable();
            $table->string('section_footer_color', 100)->nullable();
            $table->string('section_div_first_color', 100)->nullable();
            $table->string('section_div_second_color', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_styles');
    }
}
