<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('chats', function (Blueprint $table) {
        $table->string('user_name')->after('user_id'); // user_id の直後に追加
    });
}

public function down()
{
    Schema::table('chats', function (Blueprint $table) {
        $table->dropColumn('user_name');
    });
}

};
