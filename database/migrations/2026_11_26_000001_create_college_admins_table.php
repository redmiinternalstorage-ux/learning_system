<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public bool $withinTransaction = false;

    public function up(): void
    {
        // College Admin accounts — one per college
        Schema::create('college_admins', function (Blueprint $table) {
            $table->id();
            $table->string('college_name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('website')->nullable();
            $table->string('logo')->nullable();         // uploaded logo path
            $table->string('contact_person')->nullable(); // principal / coordinator name
            $table->string('domain')->nullable();         // linked campus_domain
            $table->enum('status', ['active', 'inactive', 'pending'])->default('pending');
            $table->text('notes')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        // Add college_admin_id to campus_students so students belong to a college admin
        Schema::table('campus_students', function (Blueprint $table) {
            $table->foreignId('college_admin_id')->nullable()->after('approved_by')
                  ->constrained('college_admins')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('campus_students', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\CollegeAdmin::class);
            $table->dropColumn('college_admin_id');
        });
        Schema::dropIfExists('college_admins');
    }
};
