<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('events', function (Blueprint $table) {
            $table->boolean('is_paid')->default(false)->comment('有料イベントか');
            $table->decimal('price', 10, 2)->nullable()->comment('イベント価格');
        });
    }

    public function down() {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['is_paid', 'price']);
        });
    }
};
