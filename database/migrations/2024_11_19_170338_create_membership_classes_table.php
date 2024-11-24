<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembershipClassesTable extends Migration
{
    public function up()
    {
        Schema::create('membership_classes', function (Blueprint $table) {
            $table->increments('id'); // 主キー
            $table->string('m_name', 50); // 会員区分名（例: 無料会員、有料会員）
            $table->string('name')->nullable(); // 詳細な説明
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('membership_classes');
    }
}
