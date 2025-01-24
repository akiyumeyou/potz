<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateYoutubeTable extends Migration
{
    public function up()
    {
        Schema::create('youtube', function (Blueprint $table) {
            $table->id();
            $table->string('youtube_link');
            $table->text('comment')->nullable();
            $table->string('category');
            $table->unsignedInteger('like_count')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('youtube');
    }
}
