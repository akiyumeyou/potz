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
        Schema::table('requests', function (Blueprint $table) {
            $table->unsignedBigInteger('confirmed_by')->nullable()->after('supporter_id');
            // 必要に応じて外部キー制約を追加
            $table->foreign('confirmed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropForeign(['confirmed_by']);
            $table->dropColumn('confirmed_by');
        });
    }

};
