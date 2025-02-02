<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('event_participants', function (Blueprint $table) {
            $table->integer('amount_paid')->default(0)->after('payment_status'); // 入金金額（デフォルト0）
        });
    }

    public function down()
    {
        Schema::table('event_participants', function (Blueprint $table) {
            $table->dropColumn('amount_paid');
        });
    }
};
