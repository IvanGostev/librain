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
        try {
            Schema::table('ratings', function (Blueprint $table) {
                $table->dropForeign('ratings_user_id_foreign');
            });
        } catch (\Exception $e) {
        }

        try {
            Schema::table('ratings', function (Blueprint $table) {
                $table->dropUnique('ratings_user_id_book_id_unique');
            });
        } catch (\Exception $e) {
        }

        Schema::table('ratings', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });

        if (!Schema::hasColumn('ratings', 'ip_address')) {
            Schema::table('ratings', function (Blueprint $table) {
                $table->string('ip_address', 45)->nullable()->after('user_id');
            });
        }

        try {
            Schema::table('ratings', function (Blueprint $table) {
                $table->foreign('user_id', 'ratings_user_id_fk_guest')->references('id')->on('users')->nullOnDelete();
            });
        } catch (\Exception $e) {
        }

        try {
            Schema::table('ratings', function (Blueprint $table) {
                $table->unique(['user_id', 'book_id'], 'ratings_user_book_unique_v2');
            });
        } catch (\Exception $e) {
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

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

        });
    }
};
