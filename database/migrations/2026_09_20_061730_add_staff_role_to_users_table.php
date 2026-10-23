<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        // SQLite doesn't support modifying enums, so we add a 'staff' column-level check via raw SQL.
        // For MySQL/MariaDB we modify the enum; for SQLite we just allow it via the app layer.
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('student','instructor','admin','staff') NOT NULL DEFAULT 'student'");
        }
        // For SQLite (dev), the role column is a string — no change needed since SQLite doesn't enforce enums.
    }

    public function down(): void {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('student','instructor','admin') NOT NULL DEFAULT 'student'");
        }
    }
};
