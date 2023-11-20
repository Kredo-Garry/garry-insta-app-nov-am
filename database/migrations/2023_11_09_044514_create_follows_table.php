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

        # Users table
        # id                name
        # 1                 John Smith
        # 2                 David Monroe

        # Ex. John Smith --> Auth user (ID: 1)

        Schema::create('follows', function (Blueprint $table) {
            $table->unsignedBigInteger('follower_id');  //id of the follower , ex: ID of 1 (follower)
            $table->unsignedBigInteger('following_id'); //id of the user followed by Auth user, ex: ID 2 (following)

            $table->foreign('follower_id')->references('id')->on('users');
            $table->foreign('following_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('follows');
    }
};
