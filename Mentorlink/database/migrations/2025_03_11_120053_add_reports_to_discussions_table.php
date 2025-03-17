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
            $table->json('reports')->nullable()->after('message'); // Add reports column
        });
    }

    public function down()
    {
        Schema::table('discussions', function (Blueprint $table) {
            $table->dropColumn('reports'); // Remove reports column if rolling back
        });
    }
};
