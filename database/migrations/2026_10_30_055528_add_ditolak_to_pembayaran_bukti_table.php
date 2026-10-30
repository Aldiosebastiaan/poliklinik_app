<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE pembayaran_bukti DROP CONSTRAINT pembayaran_bukti_status_check');
        DB::statement("ALTER TABLE pembayaran_bukti ADD CONSTRAINT pembayaran_bukti_status_check CHECK (status::text IN ('pending', 'verified', 'lunas', 'ditolak'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("UPDATE pembayaran_bukti SET status = 'pending' WHERE status = 'ditolak'");
        DB::statement('ALTER TABLE pembayaran_bukti DROP CONSTRAINT pembayaran_bukti_status_check');
        DB::statement("ALTER TABLE pembayaran_bukti ADD CONSTRAINT pembayaran_bukti_status_check CHECK (status::text IN ('pending', 'verified', 'lunas'))");
    }
};
