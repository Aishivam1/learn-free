<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            // Add category column (nullable for existing records)
            $table->string('category')->nullable()->after('description');

            // Add difficulty column (nullable for existing records)
            $table->string('difficulty')->nullable()->after('category');
        });
    }

    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['category', 'difficulty']);
        });
    }
};
