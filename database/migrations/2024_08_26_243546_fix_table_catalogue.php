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
        Schema::table('finance_catalogue_item', function (Blueprint $table) {
            $table->string('class_number', 100)->nullable();
            $table->integer('is_generated')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('finance_catalogue_item', function (Blueprint $table) {
            $table->dropColumn('class_number');
            $table->dropColumn('is_generated');
        });
    }
};
