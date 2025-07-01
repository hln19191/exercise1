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
        Schema::table('roles', function (Blueprint $table) {
            $table->softDeletes(); // adds deleted_at column
            $table->unsignedBigInteger('deleted_by')->nullable()->after('deleted_at');          
        });

        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes(); // adds deleted_at column
            $table->unsignedBigInteger('deleted_by')->nullable()->after('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn(['deleted_at', 'deleted_by']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['deleted_at', 'deleted_by']);
        });
    }
};
