<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('meets', function (Blueprint $table) {
            $table->id(); // 主キー
            $table->unsignedBigInteger('meet_room_id'); // 紐づくチャットルームID
            $table->unsignedBigInteger('sender_id'); // メッセージ送信者
            $table->text('message'); // メッセージ内容
            $table->timestamps();

            // 外部キー制約
            $table->foreign('meet_room_id')->references('id')->on('meet_rooms')->onDelete('cascade');
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meets');
    }
};
