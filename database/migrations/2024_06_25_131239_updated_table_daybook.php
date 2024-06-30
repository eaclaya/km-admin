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
        Schema::table('finance_daybook_entry', function (Blueprint $table) {
            $table->string('model');
            $table->string('model_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('finance_daybook_entry', function (Blueprint $table) {
            $table->dropColumn('model');
            $table->dropColumn('model_id');
        });
    }
};
