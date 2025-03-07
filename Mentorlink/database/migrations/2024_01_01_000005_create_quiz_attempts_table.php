<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->integer('score');
            $table->boolean('passed')->default(false);
            $table->timestamp('attempted_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('quiz_attempts');
    }
};
