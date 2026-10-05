<?php

namespace Database\Seeders;

use App\Enums\CaseStatus;
use App\Enums\CompanyStatus;
use App\Enums\DeadlineType;
use App\Enums\HearingStatus;
use App\Enums\InvoiceStatus;
use App\Enums\LegalForm;
use App\Enums\PartnerRole;
use App\Enums\ProcedureStatus;
use App\Enums\ProcedureType;
use App\Models\CaseType;
use App\Models\Client;
use App\Models\Company;
use App\Models\CompanyDeadline;
use App\Models\Court;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\LegalCase;
use App\Models\Opponent;
use App\Models\Payment;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        if (Client::query()->exists()) {
            return;
        }

        $lawyer = User::where('email', 'lawyer@lawfirm.test')->first();
        $admin = User::where('email', 'admin@lawfirm.test')->first();

        $client = Client::create([
            'type' => 'individual',
            'name' => 'عميل تجريبي',
            'name_en' => 'Demo Client',
            'email' => 'client@lawfirm.test',
            'city' => 'القاهرة',
            'created_by' => $admin?->id,
        ]);

        $portalUser = User::create([
            'name' => $client->name,
            'email' => 'client@lawfirm.test',
            'password' => 'password',
            'client_id' => $client->id,
        ]);
        $portalUser->syncRoles('client');
        $client->update(['user_id' => $portalUser->id]);

        $corporateClient = Client::create([
            'type' => 'company',
            'name' => 'شركة تجريبية',
            'name_en' => 'Demo Trading Co.',
            'commercial_register_no' => 'DEMO-CR-001',
            'tax_number' => 'DEMO-TAX-001',
            'email' => 'info@demo-company.test',
            'city' => 'القاهرة',
            'created_by' => $admin?->id,
        ]);

        $corporateClient->contacts()->create([
            'name' => 'جهة اتصال تجريبية',
            'position' => 'المدير المالي',
            'is_primary' => true,
        ]);

        $client->powersOfAttorney()->create([
            'number' => 'DEMO-POA-001',
            'notary_office' => 'مكتب توثيق تجريبي',
            'issued_at' => now()->subMonths(6),
            'expires_at' => now()->addYears(2),
            'lawyer_ids' => [$lawyer?->id],
            'scope' => 'توكيل تجريبي عام في القضايا',
        ]);

        $opponent = Opponent::create([
            'name' => 'خصم تجريبي',
            'type' => 'company',
            'lawyer_name' => 'محامٍ تجريبي',
        ]);

        $case = LegalCase::create([
            'title' => 'قضية تجريبية - مطالبة بقيمة أعمال',
            'case_number' => 'DEMO-1520',
            'case_year' => now()->year,
            'client_id' => $client->id,
            'client_role' => 'plaintiff',
            'case_type_id' => CaseType::where('name', 'تجاري')->value('id'),
            'court_id' => Court::where('name', 'محكمة القاهرة الاقتصادية')->value('id'),
            'circuit' => 'دائرة تجريبية',
            'lead_lawyer_id' => $lawyer?->id,
            'status' => CaseStatus::IN_PROGRESS,
            'subject' => 'بيانات تجريبية لعرض سير العمل فقط',
            'claim_amount' => 850000,
            'filed_at' => now()->subMonths(2),
            'created_by' => $admin?->id,
        ]);
        $case->lawyers()->sync(array_filter([$lawyer?->id, $admin?->id]));
        $case->opponents()->attach($opponent->id, ['role' => 'defendant']);

        $case->hearings()->createMany([
            ['scheduled_at' => now()->subWeeks(3)->setTime(10, 0), 'type' => 'pleading', 'status' => HearingStatus::HELD, 'lawyer_id' => $lawyer?->id, 'outcome' => 'نتيجة جلسة تجريبية', 'next_hearing_at' => now()->addDays(3)],
            ['scheduled_at' => now()->addDays(3)->setTime(10, 0), 'type' => 'evidence', 'status' => HearingStatus::SCHEDULED, 'lawyer_id' => $lawyer?->id, 'requirements' => 'متطلبات جلسة تجريبية'],
        ]);

        $case->activities()->create([
            'user_id' => $lawyer?->id,
            'type' => 'memo',
            'title' => 'إعداد مذكرة تجريبية',
            'occurred_at' => now()->subDays(5),
            'hours_spent' => 4,
        ]);

        $company = Company::create([
            'client_id' => $corporateClient->id,
            'name' => 'شركة تجريبية ذ.م.م',
            'name_en' => 'Demo Trading LLC',
            'legal_form' => LegalForm::LLC,
            'status' => CompanyStatus::ACTIVE,
            'issued_capital' => 500000,
            'paid_capital' => 500000,
            'commercial_register_no' => 'DEMO-CR-001',
            'commercial_register_office' => 'القاهرة',
            'commercial_register_expires_at' => now()->addMonths(2),
            'tax_card_no' => 'DEMO-TAX-001',
            'gafi_file_no' => 'DEMO-GAFI-001',
            'incorporated_at' => now()->subYears(4),
            'law' => 'بيانات قانونية تجريبية',
            'activity' => 'نشاط تجريبي',
            'governorate' => 'القاهرة',
            'responsible_lawyer_id' => $lawyer?->id,
        ]);

        $company->partners()->createMany([
            ['name' => 'شريك تجريبي أول', 'nationality' => 'مصري', 'role' => PartnerRole::MANAGING_PARTNER, 'share_percentage' => 60, 'is_signatory' => true],
            ['name' => 'شريك تجريبي ثانٍ', 'nationality' => 'مصري', 'role' => PartnerRole::PARTNER, 'share_percentage' => 40],
        ]);

        $company->procedures()->create([
            'type' => ProcedureType::CAPITAL_INCREASE,
            'status' => ProcedureStatus::UNDER_REVIEW,
            'assigned_to' => $lawyer?->id,
            'started_at' => now()->subWeeks(3),
            'submitted_at' => now()->subWeek(),
            'due_at' => now()->addWeeks(2),
            'government_fees' => 3500,
            'service_fees' => 15000,
            'description' => 'إجراء تجريبي لزيادة رأس المال',
            'checklist' => [
                ['item' => 'مستند تجريبي أول', 'done' => true],
                ['item' => 'مستند تجريبي ثانٍ', 'done' => true],
                ['item' => 'مستند تجريبي ثالث', 'done' => false],
            ],
        ]);

        CompanyDeadline::create([
            'company_id' => $company->id,
            'type' => DeadlineType::COMMERCIAL_REGISTER,
            'title' => 'موعد تجريبي لتجديد السجل التجاري',
            'due_at' => now()->addMonths(2),
            'recurring_yearly' => true,
        ]);

        $client->feeAgreements()->create([
            'legal_case_id' => $case->id,
            'type' => 'installments',
            'total_amount' => 60000,
            'advance_amount' => 20000,
            'installments_count' => 4,
            'agreed_at' => now()->subMonths(2),
        ]);

        $invoice = Invoice::create([
            'client_id' => $client->id,
            'legal_case_id' => $case->id,
            'status' => InvoiceStatus::SENT,
            'issued_at' => now()->subMonth(),
            'due_at' => now()->addWeeks(2),
        ]);
        InvoiceItem::create(['invoice_id' => $invoice->id, 'description' => 'دفعة تجريبية - أتعاب القضية', 'quantity' => 1, 'unit_price' => 20000]);
        InvoiceItem::create(['invoice_id' => $invoice->id, 'description' => 'رسوم تجريبية', 'quantity' => 1, 'unit_price' => 2500]);
        Payment::create(['client_id' => $client->id, 'invoice_id' => $invoice->id, 'amount' => 10000, 'method' => 'bank_transfer', 'paid_at' => now()->subWeeks(2), 'received_by' => $admin?->id]);

        $case->expenses()->create(['category' => 'court_fees', 'description' => 'مصروف تجريبي', 'amount' => 2500, 'spent_at' => now()->subMonths(2), 'paid_by' => $admin?->id]);

        Task::create([
            'title' => 'مهمة تجريبية للجلسة القادمة',
            'taskable_type' => LegalCase::class,
            'taskable_id' => $case->id,
            'assigned_to' => $lawyer?->id,
            'created_by' => $admin?->id,
            'priority' => 'high',
            'due_at' => now()->addDays(2),
        ]);
    }
}
