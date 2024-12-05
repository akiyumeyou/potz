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
        Schema::table('meets', function (Blueprint $table) {
            $table->string('image')->nullable()->after('message'); // 画像パス用カラムを追加
        });
    }

    public function down()
    {
        Schema::table('meets', function (Blueprint $table) {
            $table->dropColumn('image'); // カラムを削除
        });
    }

};
