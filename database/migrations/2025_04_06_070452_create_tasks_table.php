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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->enum('status', ['to do', 'in progress', 'done'])->default('to do');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->enum('category', ['frontend, backend', 'full stack', 'design', 'deployment', 'fixes'])
                ->default('full stack');
            $table->integer('story_points');

            $table->unsignedBigInteger('sprint_id');
            $table->foreign('sprint_id')->references('id')->on('sprints')->onDelete('cascade');

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->date('estimated_start');
            $table->date('estimated_finish');
            $table->integer('estimated_hours');
            $table->date('actual_start');
            $table->date('actual_finish');
            $table->integer('actual_hours');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
