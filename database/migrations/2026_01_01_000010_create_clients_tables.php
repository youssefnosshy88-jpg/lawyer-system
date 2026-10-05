<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('type', 20)->default('individual')->index();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->string('national_id', 30)->nullable()->index();
            $table->string('commercial_register_no', 50)->nullable();
            $table->string('tax_number', 50)->nullable();
            $table->string('nationality', 60)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('phone_alt', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('city', 80)->nullable();
            $table->string('occupation')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('client_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });

        Schema::create('client_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('position')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('opponents', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20)->default('individual');
            $table->string('name')->index();
            $table->string('national_id', 30)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('address')->nullable();
            $table->string('lawyer_name')->nullable();
            $table->string('lawyer_phone', 30)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('powers_of_attorney', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('number', 60);
            $table->string('notary_office')->nullable();
            $table->date('issued_at');
            $table->date('expires_at')->nullable();
            $table->string('status', 20)->default('active')->index();
            $table->json('lawyer_ids')->nullable();
            $table->text('scope')->nullable();
            $table->string('file_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['client_id', 'number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('powers_of_attorney');
        Schema::dropIfExists('opponents');
        Schema::dropIfExists('client_contacts');
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('client_id');
        });
        Schema::dropIfExists('clients');
    }
};
