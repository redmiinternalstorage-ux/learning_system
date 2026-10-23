<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::create('finance_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('account_number', 50)->unique();
            $table->string('type', 30)->default('department'); // bank, department, client, staff
            $table->decimal('balance', 15, 2)->default(0.00);
            $table->string('description', 255)->nullable();
            $table->timestamps();
        });

        Schema::create('finance_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code', 50)->unique();
            $table->string('transaction_type', 30); // transfer, salary, client_payment, deposit, expense
            $table->string('flow_type', 20); // income, expense, transfer
            $table->foreignId('from_account_id')->nullable()->constrained('finance_accounts')->nullOnDelete();
            $table->foreignId('to_account_id')->nullable()->constrained('finance_accounts')->nullOnDelete();
            $table->string('sender_name', 150);
            $table->string('receiver_name', 150);
            $table->string('client_name', 150)->nullable();
            $table->string('client_email', 150)->nullable();
            $table->foreignId('staff_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('salary_payment_id')->nullable()->constrained('salary_payments')->nullOnDelete();
            $table->decimal('amount', 15, 2);
            $table->string('status', 20)->default('completed'); // pending, approved, failed, completed
            $table->boolean('auto_approved')->default(false);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Seed initial bank account with starting balance ₹100,000
        $now = now();
        $mainAccountId = DB::table('finance_accounts')->insertGetId([
            'name'           => 'Main Bank Account',
            'account_number' => 'ACC-MAIN-1001',
            'type'           => 'bank',
            'balance'        => 100000.00,
            'description'    => 'Primary Organization Reserve Account',
            'created_at'     => $now,
            'updated_at'     => $now,
        ]);

        $hrAccountId = DB::table('finance_accounts')->insertGetId([
            'name'           => 'HR Department',
            'account_number' => 'ACC-DEPT-HR',
            'type'           => 'department',
            'balance'        => 0.00,
            'description'    => 'Human Resources Budget Account',
            'created_at'     => $now,
            'updated_at'     => $now,
        ]);

        DB::table('finance_accounts')->insert([
            [
                'name'           => 'Marketing Department',
                'account_number' => 'ACC-DEPT-MKT',
                'type'           => 'department',
                'balance'        => 0.00,
                'description'    => 'Marketing & Promotions Budget',
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
            [
                'name'           => 'IT & Tech Department',
                'account_number' => 'ACC-DEPT-IT',
                'type'           => 'department',
                'balance'        => 0.00,
                'description'    => 'Software & Infrastructure Budget',
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
            [
                'name'           => 'Operations Department',
                'account_number' => 'ACC-DEPT-OPS',
                'type'           => 'department',
                'balance'        => 0.00,
                'description'    => 'General Operations Account',
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
            [
                'name'           => 'Client Services Account',
                'account_number' => 'ACC-CLIENT-001',
                'type'           => 'client',
                'balance'        => 0.00,
                'description'    => 'Student & Client Course Payment Receiver',
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
            [
                'name'           => 'Staff Payroll Account',
                'account_number' => 'ACC-STAFF-001',
                'type'           => 'staff',
                'balance'        => 0.00,
                'description'    => 'Staff Salary Distribution Account',
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
        ]);

        // Insert initial starting deposit transaction
        DB::table('finance_transactions')->insert([
            'transaction_code' => 'TXN-INIT-10001',
            'transaction_type' => 'deposit',
            'flow_type'        => 'income',
            'from_account_id'  => null,
            'to_account_id'    => $mainAccountId,
            'sender_name'      => 'System Opening Reserve',
            'receiver_name'    => 'Main Bank Account',
            'amount'           => 100000.00,
            'status'           => 'completed',
            'auto_approved'    => true,
            'notes'            => 'Initial opening balance for Finance Department (₹100,000)',
            'created_at'       => $now,
            'updated_at'       => $now,
        ]);
    }

    public function down(): void {
        Schema::dropIfExists('finance_transactions');
        Schema::dropIfExists('finance_accounts');
    }
};
