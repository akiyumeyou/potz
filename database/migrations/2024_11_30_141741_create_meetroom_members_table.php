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
        Schema::create('meetroom_members', function (Blueprint $table) {
            $table->bigIncrements('id'); // 主キー
            $table->unsignedBigInteger('meet_room_id'); // 外部キー
            $table->unsignedBigInteger('user_id'); // 外部キー
            $table->string('role'); // 役割: requester, supporter, coordinator
            $table->boolean('is_active')->default(1); // アクティブ状態（デフォルト1）
            $table->timestamp('joined_at')->nullable(); // 参加日時
            $table->timestamp('left_at')->nullable(); // 退出日時
            $table->timestamps(); // created_at, updated_at

            // 外部キー設定
            $table->foreign('meet_room_id')->references('id')->on('meet_rooms')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('meetroom_members');
    }
};
