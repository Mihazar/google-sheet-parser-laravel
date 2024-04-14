<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sheet_total_for_year', function (Blueprint $table) {
            $table->id();
            $table->integer('spreadsheet_id');
            $table->integer('sheet_id');
            $table->char('object_type');
            $table->integer('object_id');
            $table->double('total');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sheet_total_for_year');
    }
};
