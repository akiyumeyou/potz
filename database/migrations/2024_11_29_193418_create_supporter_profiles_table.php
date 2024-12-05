<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('supporter_profiles', function (Blueprint $table) {
            $table->id(); // 主キー
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // 外部キー（usersテーブルと連携）
            $table->string('pref_photo')->nullable(); // 認証証明画像
            $table->string('ac_id')->nullable(); // 認証区分（画像アップ）
            $table->text('self_introduction')->nullable(); // 自己紹介
            $table->string('skill1')->nullable(); // スキル1
            $table->string('skill2')->nullable(); // スキル2
            $table->string('skill3')->nullable(); // スキル3
            $table->string('skill4')->nullable(); // スキル4
            $table->string('skill5')->nullable(); // スキル5
            $table->timestamps(); // 作成・更新日時
        });
    }

    public function down()
    {
        Schema::dropIfExists('supporter_profiles');
    }
};
