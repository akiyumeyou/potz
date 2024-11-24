<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategory3Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category3', function (Blueprint $table) {
            $table->id(); // 主キー
            $table->text('category3'); // カテゴリ3の名前
            $table->integer('order_no'); // 順番
            $table->integer('cost'); // コスト
            $table->timestamps(); // 作成・更新日時
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('category3');
    }
}
