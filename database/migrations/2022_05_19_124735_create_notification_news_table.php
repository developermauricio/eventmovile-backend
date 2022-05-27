<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationNewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_news', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id');
            $table->foreign('event_id')->references('id')->on('events');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('link_event_action')->nullable();
            $table->enum('type_notification', ['direct', 'programmed']);
            $table->timestamp('end_time')->nullable();
            $table->enum('send', [
                \App\NotificationNew::NOT_SEND,
                \App\NotificationNew::SEND,
            ])->default(\App\NotificationNew::NOT_SEND);
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
        Schema::dropIfExists('notification_news');
    }
}
