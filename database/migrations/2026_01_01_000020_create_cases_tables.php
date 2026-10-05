<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->string('type', 50)->nullable();
            $table->string('city', 80)->nullable();
            $table->string('address')->nullable();
            $table->string('phone', 30)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('case_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->string('color', 20)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('legal_cases', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 30)->unique();
            $table->string('case_number', 60)->nullable();
            $table->unsignedSmallInteger('case_year')->nullable();
            $table->string('title');
            $table->foreignId('client_id')->constrained()->restrictOnDelete();
            $table->string('client_role', 30)->default('plaintiff');
            $table->foreignId('case_type_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('court_id')->nullable()->constrained()->nullOnDelete();
            $table->string('circuit', 80)->nullable();
            $table->string('degree', 40)->default('first_instance');
            $table->foreignId('lead_lawyer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 30)->default('open')->index();
            $table->text('subject')->nullable();
            $table->text('description')->nullable();
            $table->decimal('claim_amount', 14, 2)->nullable();
            $table->date('filed_at')->nullable();
            $table->date('closed_at')->nullable();
            $table->text('judgment_summary')->nullable();
            $table->boolean('visible_to_client')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['case_number', 'case_year']);
        });

        Schema::create('case_lawyer', function (Blueprint $table) {
            $table->foreignId('legal_case_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->primary(['legal_case_id', 'user_id']);
        });

        Schema::create('case_opponent', function (Blueprint $table) {
            $table->foreignId('legal_case_id')->constrained()->cascadeOnDelete();
            $table->foreignId('opponent_id')->constrained()->cascadeOnDelete();
            $table->string('role', 30)->nullable();
            $table->primary(['legal_case_id', 'opponent_id']);
        });

        Schema::create('hearings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('legal_case_id')->constrained()->cascadeOnDelete();
            $table->dateTime('scheduled_at')->index();
            $table->string('type', 30)->default('pleading');
            $table->string('status', 20)->default('scheduled')->index();
            $table->string('courtroom', 60)->nullable();
            $table->foreignId('lawyer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('requirements')->nullable();
            $table->text('outcome')->nullable();
            $table->text('decision')->nullable();
            $table->date('next_hearing_at')->nullable();
            $table->boolean('reminder_sent')->default(false);
            $table->boolean('visible_to_client')->default(true);
            $table->timestamps();
        });

        Schema::create('case_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('legal_case_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 30)->default('memo');
            $table->string('title');
            $table->text('body')->nullable();
            $table->dateTime('occurred_at');
            $table->decimal('hours_spent', 5, 2)->nullable();
            $table->boolean('visible_to_client')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('case_activities');
        Schema::dropIfExists('hearings');
        Schema::dropIfExists('case_opponent');
        Schema::dropIfExists('case_lawyer');
        Schema::dropIfExists('legal_cases');
        Schema::dropIfExists('case_types');
        Schema::dropIfExists('courts');
    }
};
