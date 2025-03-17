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
        Schema::table('discussions', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->constrained('discussions')->onDelete('cascade')->after('user_id');
        });
    }

    public function down()
    {
        Schema::table('discussions', function (Blueprint $table) {
            $table->dropColumn('parent_id');
        });
    }
};
