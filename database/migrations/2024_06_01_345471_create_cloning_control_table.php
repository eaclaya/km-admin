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
        Schema::dropIfExists('cloning_control');
        Schema::create('cloning_control', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();
            $table->string('model')->nullable();
            $table->unsignedInteger('account_id')->nullable();
            $table->timestamp('from_date')->nullable();
            $table->timestamp('to_date')->nullable();
            $table->integer('is_completed')->nullable();
            $table->timestamps();
            $table->softDeletes()->nullable();
        });
        // CloningControl
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cloning_control');
    }
};
