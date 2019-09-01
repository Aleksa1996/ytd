<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYoutubeConvertsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('youtube_converts', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->ipAddress('ip');
            $table->integer('for_fd');
            $table->enum('status', ['converting', 'finished']);

            $table->integer('video_id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('youtube_converts');
    }
}
