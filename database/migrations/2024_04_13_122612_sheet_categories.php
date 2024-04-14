<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sheet_categories', function (Blueprint $table) {
            $table->id();
            $table->integer('spreadsheet_id');
            $table->integer('sheet_id');
            $table->string('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sheet_categories');
    }
};
