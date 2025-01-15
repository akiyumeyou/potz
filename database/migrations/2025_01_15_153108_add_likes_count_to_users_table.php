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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('likes_count')->default(0)->after('email'); // `email` の後に配置
        });
    }


public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('likes_count');
    });
}

};
