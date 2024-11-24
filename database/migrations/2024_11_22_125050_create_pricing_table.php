<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePricingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pricing', function (Blueprint $table) {
            $table->id(); // 主キー
            $table->unsignedBigInteger('request_id'); // 支援依頼テーブルへの外部キー
            $table->text('val')->nullable(); // 任意の値
            $table->integer('price'); // 料金
            $table->integer('order_no'); // 注文番号
            $table->timestamps(); // 作成日時・更新日時

            // 外部キー制約
            $table->foreign('request_id')->references('id')->on('requests')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pricing');
    }
}
