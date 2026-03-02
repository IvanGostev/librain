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
        Schema::table('book_series', function (Blueprint $table) {
            $table->dropForeign(['series_id']);
            $table->dropForeign(['book_id']);
            $table->dropPrimary(['series_id', 'book_id']);
            $table->id()->first();
            $table->unique(['series_id', 'book_id']);
            $table->foreign('series_id')->references('id')->on('series')->cascadeOnDelete();
            $table->foreign('book_id')->references('id')->on('books')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('book_series', function (Blueprint $table) {
            $table->dropForeign(['series_id']);
            $table->dropForeign(['book_id']);
            $table->dropUnique(['series_id', 'book_id']);
            $table->dropColumn('id');
            $table->primary(['series_id', 'book_id']);
            $table->foreign('series_id')->references('id')->on('series')->cascadeOnDelete();
            $table->foreign('book_id')->references('id')->on('books')->cascadeOnDelete();
        });
    }
};
