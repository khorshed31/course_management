<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNotesOthersToCourseLessons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void {
        Schema::table('course_lessons', function (Blueprint $table) {
            $table->text('notes')->nullable()->after('rounds');   // long free text
            $table->string('others')->nullable()->after('notes'); // short tag/remark
        });
    }
    public function down(): void {
        Schema::table('course_lessons', function (Blueprint $table) {
            $table->dropColumn(['notes','others']);
        });
    }
}
