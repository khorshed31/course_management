<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrdersToilsRoundsToCourseLessons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void {
        Schema::table('course_lessons', function (Blueprint $table) {
            $table->integer('toils')->nullable()->after('sort_order');
            $table->string('rounds')->nullable()->after('toils');
        });
    }
    public function down(): void {
        Schema::table('course_lessons', function (Blueprint $table) {
            $table->dropColumn(['toils','rounds']);
        });
    }
}
