<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->string('legal_form', 40)->default('llc');
            $table->string('status', 30)->default('under_incorporation')->index();
            $table->decimal('authorized_capital', 16, 2)->nullable();
            $table->decimal('issued_capital', 16, 2)->nullable();
            $table->decimal('paid_capital', 16, 2)->nullable();
            $table->string('currency', 5)->default('EGP');
            $table->string('commercial_register_no', 60)->nullable()->index();
            $table->string('commercial_register_office')->nullable();
            $table->date('commercial_register_expires_at')->nullable();
            $table->string('tax_card_no', 60)->nullable();
            $table->string('tax_office')->nullable();
            $table->string('gafi_file_no', 60)->nullable()->index();
            $table->string('gafi_license_no', 60)->nullable();
            $table->date('incorporated_at')->nullable();
            $table->string('law', 40)->nullable();
            $table->text('activity')->nullable();
            $table->string('address')->nullable();
            $table->string('governorate', 80)->nullable();
            $table->unsignedTinyInteger('fiscal_year_end_month')->default(12);
            $table->foreignId('responsible_lawyer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('company_partners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('nationality', 60)->nullable();
            $table->string('id_type', 20)->default('national_id');
            $table->string('id_number', 40)->nullable();
            $table->string('role', 40)->default('partner');
            $table->decimal('share_percentage', 5, 2)->nullable();
            $table->unsignedBigInteger('shares_count')->nullable();
            $table->decimal('share_value', 16, 2)->nullable();
            $table->boolean('is_signatory')->default(false);
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('company_procedures', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 30)->unique();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('type', 40)->default('incorporation');
            $table->string('status', 30)->default('draft')->index();
            $table->string('authority', 80)->default('GAFI');
            $table->string('authority_reference', 80)->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->date('started_at')->nullable();
            $table->date('submitted_at')->nullable();
            $table->date('due_at')->nullable();
            $table->date('completed_at')->nullable();
            $table->decimal('government_fees', 12, 2)->nullable();
            $table->decimal('service_fees', 12, 2)->nullable();
            $table->json('checklist')->nullable();
            $table->text('description')->nullable();
            $table->text('result')->nullable();
            $table->boolean('visible_to_client')->default(true);
            $table->timestamps();
        });

        Schema::create('company_deadlines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('type', 40)->default('commercial_register');
            $table->string('title');
            $table->date('due_at')->index();
            $table->string('status', 20)->default('pending')->index();
            $table->boolean('recurring_yearly')->default(false);
            $table->boolean('reminder_sent')->default(false);
            $table->date('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_deadlines');
        Schema::dropIfExists('company_procedures');
        Schema::dropIfExists('company_partners');
        Schema::dropIfExists('companies');
    }
};
