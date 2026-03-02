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
        Schema::table('books', function (Blueprint $table) {
            $table->boolean('hide_download_button')->default(false);
            $table->boolean('hide_txt')->default(false);
            $table->boolean('hide_fb2')->default(false);
            $table->boolean('hide_epub')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn([
                'hide_download_button',
                'hide_txt',
                'hide_fb2',
                'hide_epub'
            ]);
        });
    }
};
