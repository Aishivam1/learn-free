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
            // Modify the status column to include 'rejected'
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->change();

            // Add rejection_reason column
            $table->text('rejection_reason')->nullable()->after('status');
        });
    }

    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            // Revert the status column back to the original state
            $table->enum('status', ['pending', 'approved'])->default('pending')->change();

            // Drop rejection_reason column
            $table->dropColumn('rejection_reason');
        });
    }
};
