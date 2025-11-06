<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdminFieldsToContactMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->string('status', 20)->default('open')->index(); // open|replied|closed
            $table->boolean('is_starred')->default(false)->index();
            $table->timestamp('first_replied_at')->nullable()->index();
            $table->unsignedInteger('reply_count')->default(0);
            $table->softDeletes(); // adds deleted_at
        });
    }
    public function down(): void {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropColumn(['status','is_starred','first_replied_at','reply_count']);
            $table->dropSoftDeletes();
        });
    }
}
