<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('membership_id')->default(1)->after('remember_token'); // デフォルト値1（通常会員）
            $table->boolean('email_approval')->default(false)->after('membership_id'); // メール確認
            $table->string('real_name', 255)->nullable()->after('email_approval'); // 本名
            $table->string('real_name_kana', 255)->nullable()->after('real_name'); // 本名のカナ
            $table->string('prefecture', 100)->nullable()->after('real_name_kana'); // 都道府県
            $table->string('address1', 255)->nullable()->after('prefecture'); // 住所1
            $table->string('address2', 255)->nullable()->after('address1'); // 住所2
            $table->string('tel', 20)->nullable()->after('address2'); // 電話番号
            $table->enum('gender', ['male', 'female', 'other'])->default('other')->after('tel'); // 性別
            $table->date('birthday')->nullable()->after('gender'); // 生年月日
            $table->string('icon', 255)->nullable()->after('birthday'); // アイコン画像

            // 外部キー制約
            $table->foreign('membership_id')
            ->references('id')
            ->on('membership_classes')
            ->onDelete('cascade'); // 会員種別削除時に関連ユーザーも削除
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['membership_id']);
            $table->dropColumn([
                'membership_id',
                'email_approval',
                'real_name',
                'real_name_kana',
                'prefecture',
                'address1',
                'address2',
                'tel',
                'gender',
                'birthday',
                'icon',
            ]);
        });
    }
}
