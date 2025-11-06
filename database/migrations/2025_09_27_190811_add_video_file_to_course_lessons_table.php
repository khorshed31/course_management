<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVideoFileToCourseLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_lessons', function (Blueprint $table) {
            $table->string('video_file_path')->nullable()->after('video_url');
            $table->string('video_mime')->nullable()->after('video_file_path');
            $table->unsignedBigInteger('video_size')->nullable()->after('video_mime');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('course_lessons', function (Blueprint $table) {
            $table->dropColumn(['video_file_path','video_mime','video_size']);
        });
    }
}
