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
        Schema::create('nft_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nft_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('balance')->default(0);
            $table->timestamps();
        });

        Schema::table('launchpads', function (Blueprint $table) {
            $table->string('nft_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nft_user');
        Schema::table('launchpads', function (Blueprint $table) {
            $table->dropColumn('nft_type');
        });
    }
};
