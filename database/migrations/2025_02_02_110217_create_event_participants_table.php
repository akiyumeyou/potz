<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('event_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('payment_method')->nullable()->comment('支払い方法');
            $table->tinyInteger('status')->default(0)->comment('0:未承認, 1:承認済み');
            $table->tinyInteger('payment_status')->default(0)->comment('0:未払い, 1:支払い済み');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('event_participants');
    }
};

