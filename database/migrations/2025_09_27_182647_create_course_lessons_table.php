<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('chapter_id')->constrained('course_chapters')->cascadeOnDelete();
            $table->string('title');

            // content type: video url / file / text
            $table->enum('type', ['video', 'file', 'text'])->default('text');

            // for video
            $table->string('video_provider')->nullable(); // youtube, vimeo, local
            $table->string('video_url')->nullable();

            // for file (pdf/image/zip etc.)
            $table->string('file_path')->nullable(); // relative path in /public
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();

            // for text
            $table->longText('content_text')->nullable();

            $table->unsignedInteger('duration_seconds')->nullable(); // optional
            $table->unsignedInteger('sort_order')->default(1);
            // $table->boolean('is_free_preview')->default(false);
            $table->boolean('status')->default(true);

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
        Schema::dropIfExists('course_lessons');
    }
}
