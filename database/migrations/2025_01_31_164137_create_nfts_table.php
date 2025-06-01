<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nfts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('chainId')->nullable();
            $table->string('contract')->nullable()->unique();
            $table->json('abi')->nullable();
            $table->string('name');
            $table->string('symbol');
            $table->json('metadata')->nullable();
            $table->string('image')->nullable();
            $table->string('type')->default('verification');
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('nfts');
    }
};
