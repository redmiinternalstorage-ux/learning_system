<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('chat_messages', function (Blueprint $table) {
            if (!Schema::hasColumn('chat_messages', 'assigned_to')) {
                $table->foreignId('assigned_to')->nullable()->after('admin_id')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('chat_messages', 'transferred_by')) {
                $table->foreignId('transferred_by')->nullable()->after('assigned_to')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('chat_messages', 'transfer_note')) {
                $table->text('transfer_note')->nullable()->after('transferred_by');
            }
        });

        Schema::table('support_tickets', function (Blueprint $table) {
            if (!Schema::hasColumn('support_tickets', 'assigned_to')) {
                $table->foreignId('assigned_to')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('support_tickets', 'transferred_by')) {
                $table->foreignId('transferred_by')->nullable()->after('assigned_to')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('support_tickets', 'transfer_note')) {
                $table->text('transfer_note')->nullable()->after('transferred_by');
            }
        });
    }

    public function down(): void {
        Schema::table('chat_messages', function (Blueprint $table) {
            foreach (['assigned_to', 'transferred_by', 'transfer_note'] as $col) {
                if (Schema::hasColumn('chat_messages', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
        Schema::table('support_tickets', function (Blueprint $table) {
            foreach (['assigned_to', 'transferred_by', 'transfer_note'] as $col) {
                if (Schema::hasColumn('support_tickets', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
