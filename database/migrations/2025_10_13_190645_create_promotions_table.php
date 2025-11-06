<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromotionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id')->nullable()->comment('0 for All Courses, specific course ID otherwise');
            $table->enum('discount_type', ['null', 'timer', 'first_some_student', 'special_day'])->nullable();
            $table->datetime('start_time')->nullable()->comment('Used for timer discount');
            $table->datetime('end_time')->nullable()->comment('Used for timer discount');
            $table->integer('student_limit')->nullable()->comment('Used for first_some_student discount');
            $table->string('day_title')->nullable()->comment('Used for special_day discount');
            $table->date('start_date')->nullable()->comment('Special day start date');
            $table->date('end_date')->nullable()->comment('Special day end date');
            $table->enum('discount_value_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('discount_value', 10, 2)->comment('Value of the discount');
            $table->boolean('status')->default(1)->comment('1 for active, 0 for inactive');
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
        Schema::dropIfExists('promotions');
    }
}
