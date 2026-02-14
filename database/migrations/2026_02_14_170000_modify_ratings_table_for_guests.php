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
        // 1. Drop old Foreign Key if exists
        try {
            Schema::table('ratings', function (Blueprint $table) {
                $table->dropForeign('ratings_user_id_foreign');
            });
        } catch (\Exception $e) {
            // Likely didn't exist
        }

        // 2. Drop old Unique Index if exists
        try {
            Schema::table('ratings', function (Blueprint $table) {
                $table->dropUnique('ratings_user_id_book_id_unique');
            });
        } catch (\Exception $e) {
            // Likely didn't exist
        }

        // 3. Modify user_id to be nullable
        Schema::table('ratings', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });

        // 4. Add ip_address column if not exists
        if (!Schema::hasColumn('ratings', 'ip_address')) {
            Schema::table('ratings', function (Blueprint $table) {
                $table->string('ip_address', 45)->nullable()->after('user_id');
            });
        }

        // 5. Add new Foreign Key (using a specific name to avoid collision)
        try {
            Schema::table('ratings', function (Blueprint $table) {
                $table->foreign('user_id', 'ratings_user_id_fk_guest')->references('id')->on('users')->nullOnDelete();
            });
        } catch (\Exception $e) {
            // Already exists?
        }

        // 6. Add new Unique Constraint
        try {
            Schema::table('ratings', function (Blueprint $table) {
                // Unique index allowing user_id NULLs (standard SQL) or specific logic?
                // In MySQL, unique index allows multiple NULLs.
                // So we can re-use the same columns.
                // We give it a new name to be safe.
                $table->unique(['user_id', 'book_id'], 'ratings_user_book_unique_v2');
            });
        } catch (\Exception $e) {
            // Already exists
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse operations

        try {
            Schema::table('ratings', function (Blueprint $table) {
                $table->dropForeign('ratings_user_id_fk_guest');
            });
        } catch (\Exception $e) {
        }

        try {
            Schema::table('ratings', function (Blueprint $table) {
                $table->dropUnique('ratings_user_book_unique_v2');
            });
        } catch (\Exception $e) {
        }

        if (Schema::hasColumn('ratings', 'ip_address')) {
            Schema::table('ratings', function (Blueprint $table) {
                $table->dropColumn('ip_address');
            });
        }

        Schema::table('ratings', function (Blueprint $table) {
            // We can't easily revert nullable->change() if there are nulls.
            // So we skip enforcing not null, or we assume data is clean.
            // For safety, we keep it nullable or attempt change.
            // $table->unsignedBigInteger('user_id')->nullable(false)->change(); 

            // Re-add old FK and Unique if needed, but names might collide if we are not careful.
            // $table->foreign('user_id', 'ratings_user_id_foreign')...
        });
    }
};
