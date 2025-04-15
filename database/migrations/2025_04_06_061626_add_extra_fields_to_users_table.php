<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nickname')->nullable()->default('user');
            $table->integer('hour_value')->default(0);
            $table->enum('work_time', ['full', 'part time'])->default('part time');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('nickname');
            $table->dropColumn('hour_value');
            $table->dropColumn('work_time');
            $table->dropColumn('status');
            $table->dropSoftDeletes();
        });
    }
};
