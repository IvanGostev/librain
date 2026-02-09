<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->string('file_txt')->nullable()->after('cover_image');
            $table->string('file_fb2')->nullable()->after('file_txt');
            $table->string('file_epub')->nullable()->after('file_fb2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn(['file_txt', 'file_fb2', 'file_epub']);
        });
    }
};
