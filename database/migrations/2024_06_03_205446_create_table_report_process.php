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
        Schema::dropIfExists('report_process');
        Schema::create('report_process', function (Blueprint $table) {
            // ReportProcess
            $table->increments('id');
            $table->string('report');
            $table->string('file');
            $table->integer('status');
            $table->integer('rows')->nullable();
            $table->integer('count_rows')->nullable();
            $table->timestamps();
            $table->softDeletes()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_process');
    }
};
