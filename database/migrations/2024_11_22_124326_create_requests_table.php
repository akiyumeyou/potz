<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id(); // 主キー
            $table->text('val')->nullable(); // 任意のテキスト
            $table->text('contents')->nullable(); // 依頼内容
            $table->unsignedBigInteger('category3_id')->nullable(); // カテゴリ3のID
            $table->timestamp('date')->nullable(); // 日付と時刻
            $table->integer('time')->nullable(); // 時間
            $table->text('spot')->nullable(); // 場所の詳細
            $table->text('address')->nullable(); // 住所
            $table->float('lat')->nullable(); // 緯度
            $table->float('lng')->nullable(); // 経度
            $table->text('parking')->nullable(); // 駐車場情報
            $table->text('requester')->nullable(); // 依頼者情報
            $table->unsignedBigInteger('supporter_id')->nullable(); // サポート提供者（外部キー）
            $table->text('supporter_check')->nullable(); // サポート提供者の確認状態
            $table->text('requester_check')->nullable(); // 依頼者の確認状態
            $table->unsignedInteger('status_id')->nullable(); // ステータスID
            $table->unsignedInteger('price_id')->nullable(); // 価格ID
            $table->integer('cost')->nullable(); // コスト
            $table->unsignedBigInteger('requester_id'); // 依頼者ID（外部キー）
            $table->geometry('geom')->nullable(); // ジオメトリ情報 (POINT)
            $table->text('cancel_flag')->nullable(); // キャンセルフラグ
            $table->text('cancel_target')->nullable(); // キャンセル対象
            $table->integer('money')->nullable(); // 支払い金額
            $table->timestamps(); // 作成・更新日時

            // 外部キー制約
            $table->foreign('supporter_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('requester_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('requests');
    }
}
