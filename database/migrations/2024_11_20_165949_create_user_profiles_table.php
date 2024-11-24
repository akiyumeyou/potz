<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserProfilesTable extends Migration
{
    public function up()
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Usersテーブルとの外部キー
            $table->unsignedInteger('membership_id')->nullable(); // Membership_classテーブルとの外部キー
            $table->string('name')->nullable(); // 名前
            $table->string('name_kana')->nullable(); // 名前のふりがな
            $table->string('post', 8)->nullable(); // 郵便番号
            $table->string('address')->nullable(); // 住所
            $table->string('tel', 15)->nullable(); // 電話番号
            $table->enum('sex', ['male', 'female', 'other'])->nullable(); // 性別
            $table->date('birthday')->nullable(); // 生年月日
            $table->string('icon')->nullable(); // プロフィール画像
            $table->string('pref_photo')->nullable(); // 好きな画像
            $table->timestamps();

            // 外部キー制約
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('membership_id')->references('id')->on('membership_classes');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_profiles');
    }
}
