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
        $table->id();
        $table->unsignedBigInteger('requester_id');
        $table->unsignedBigInteger('supporter_id')->nullable();
        $table->unsignedBigInteger('meetroom_id')->nullable(); // UNSIGNED を一致させる
        $table->foreign('meetroom_id')->references('id')->on('meet_rooms')->onDelete('set null');
        $table->unsignedTinyInteger('status')->default(0); // 0: pending, 3: matched
        $table->decimal('cost', 10, 2)->nullable(); // 時給
        $table->integer('time')->nullable(); // 支援時間
        $table->decimal('transportation_costs', 10, 2)->nullable();
        $table->decimal('sonotacost1', 10, 2)->nullable();
        $table->decimal('sonotacost2', 10, 2)->nullable();
        $table->decimal('sonotacost3', 10, 2)->nullable();
        $table->decimal('costkei', 10, 2)->nullable(); // 総合計
        $table->text('remarks')->nullable(); // 備考
        $table->boolean('syousyu_flg')->default(false);
        $table->timestamp('matched_at')->nullable();
        $table->timestamp('closed_at')->nullable();
        $table->timestamps();

        // 外部キー制約（必要に応じて）
        $table->foreign('requester_id')->references('id')->on('users')->onDelete('cascade');
        $table->foreign('supporter_id')->references('id')->on('users')->onDelete('set null');
        $table->foreign('meetroom_id')->references('id')->on('meetrooms')->onDelete('set null');
    });
}

};
