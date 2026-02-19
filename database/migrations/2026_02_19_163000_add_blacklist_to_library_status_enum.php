<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE library_entries MODIFY COLUMN status ENUM('planned', 'reading', 'dropped', 'finished', 'blacklist') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: Reversing this might cause data loss for 'blacklist' entries if not handled.
        // We will just set blacklist entries to null before reverting.
        DB::table('library_entries')->where('status', 'blacklist')->update(['status' => null]);
        DB::statement("ALTER TABLE library_entries MODIFY COLUMN status ENUM('planned', 'reading', 'dropped', 'finished') NULL");
    }
};
