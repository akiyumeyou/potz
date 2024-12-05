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
            $table->id(); // 主キー
            $table->foreignId('meet_room_id')->constrained('meet_rooms')->onDelete('cascade'); // 外部キー
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // 外部キー
            $table->string('role'); // 役割: requester, supporter, coordinator
            $table->boolean('is_active')->default(true); // アクティブ状態
            $table->timestamp('joined_at')->nullable(); // 参加日時
            $table->timestamp('left_at')->nullable(); // 退出日時
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('meetroom_members');
    }
    };
