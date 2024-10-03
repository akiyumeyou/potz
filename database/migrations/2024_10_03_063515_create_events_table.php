<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->bigIncrements('id'); // ID
            $table->string('title'); // イベントタイトル
            $table->date('event_date'); // 開催日
            $table->time('start_time'); // 開始時間
            $table->time('end_time'); // 終了時間
            $table->text('content'); // イベント内容
            $table->string('zoom_url'); // Zoom URL
            $table->boolean('recurring')->default(0); // 定期開催フラグ
            $table->boolean('holiday')->default(0); // 休みフラグ
            $table->string('recurring_type')->default('once'); // 開催頻度
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // 作成者ID（外部キー）
            $table->string('image_path')->nullable(); // イメージ画像のパス
            $table->timestamps(); // 作成日時、更新日時
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
}
