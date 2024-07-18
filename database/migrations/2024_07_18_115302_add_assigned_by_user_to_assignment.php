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
         Schema::table('assignments', function (Blueprint $table) {
            $table->unsignedBigInteger('assigned_by_user_id')->nullable()->after('user_id');
            $table->foreign('assigned_by_user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->dropForeign(['assigned_by_user_id']);
            $table->dropColumn('assigned_by_user_id');
        });
    }
};
