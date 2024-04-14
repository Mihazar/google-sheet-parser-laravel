<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('google_sheet', function (Blueprint $table) {
            $table->id();
            $table->string('spreadsheet_id');
            $table->string('range');
            $table->string('title');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('google_sheet');
    }
};
