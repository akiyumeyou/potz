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
        Schema::create('matchings', function (Blueprint $table) {
            $table->bigIncrements('id'); // 主キー
            $table->unsignedBigInteger('request_id')->nullable(); // リクエストID
            $table->unsignedBigInteger('matched_by_user_id')->nullable(); // マッチング作成者ID
            $table->timestamp('receipt_issued_at')->nullable(); // 領収書発行日時
            $table->unsignedBigInteger('requester_id'); // 依頼者ID
            $table->unsignedBigInteger('supporter_id')->nullable(); // サポーターID
            $table->unsignedBigInteger('meetroom_id')->nullable(); // ミートルームID
            $table->unsignedTinyInteger('status')->default(0); // ステータス（デフォルト: 0）
            $table->decimal('cost', 10, 2)->nullable(); // 時給
            $table->decimal('time', 4, 1)->nullable(); // 支援時間
            $table->integer('distance')->default(2); // 距離
            $table->decimal('transportation_costs', 10, 2)->nullable(); // 交通費
            $table->decimal('sonotacost1', 10, 2)->nullable(); // その他費用1
            $table->decimal('sonotacost2', 10, 2)->nullable(); // その他費用2
            $table->decimal('sonotacost3', 10, 2)->nullable(); // その他費用3
            $table->decimal('costkei', 10, 2)->nullable(); // 総合計
            $table->text('remarks')->nullable(); // 備考
            $table->boolean('syousyu_flg')->default(false); // 0: 未発行, 1: 発行済み
            $table->timestamp('matched_at')->nullable(); // マッチング日時
            $table->timestamp('closed_at')->nullable(); // 終了日時
            $table->timestamps(); // created_at, updated_at

            // 外部キー制約
            $table->foreign('request_id')->references('id')->on('requests')->onDelete('set null');
            $table->foreign('matched_by_user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('requester_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('supporter_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('meetroom_id')->references('id')->on('meet_rooms')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('matchings');
    }
};
