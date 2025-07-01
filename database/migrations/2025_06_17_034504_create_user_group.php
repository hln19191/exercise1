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
        Schema::create('user_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

         Schema::table('users', function (Blueprint $table) {
            $table->foreignId('user_group_id')->nullable()->constrained('user_groups')->onDelete('set null');
            $table->boolean('is_active')->default(true)->after('user_group_id');
            $table->string('photo')->nullable()->after('email');
       });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_group');
    }
};
