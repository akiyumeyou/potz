<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('meet_rooms', function (Blueprint $table) {
            $table->id(); // 主キー
            $table->unsignedBigInteger('request_id'); // 紐づく依頼ID
            $table->integer('max_supporters')->default(3); // デフォルト値を3に設定
            $table->timestamps();

            // 外部キー制約
            $table->foreign('request_id')->references('id')->on('requests')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('meet_rooms');
    }
};
