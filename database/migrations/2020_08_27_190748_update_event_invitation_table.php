<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEventInvitationTable extends Migration
{
    
    public function up()
    {
        Schema::table('event_invitations', function (Blueprint $table) {
            $table->string('name');
        });
    }

    
    public function down()
    {
        Schema::table('event_invitations', function(Blueprint $table) {
            $table->dropColumn('name');
        });
    }
}
