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
            'name' => 'خالد عبد الرحمن',
            'name_en' => 'Khaled Abdelrahman',
            'national_id' => null,
            'phone' => null,
            'email' => 'client@lawfirm.test',
            'city' => 'القاهرة',
            'address' => 'مدينة نصر، القاهرة',
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
            'name' => 'شركة النيل للتجارة',
            'name_en' => 'Nile Trading Co.',
            'commercial_register_no' => '123456',
            'tax_number' => '987-654-321',
            'phone' => null,
            'email' => 'info@nile-trading.test',
            'city' => 'القاهرة',
            'created_by' => $admin?->id,
        ]);

        $corporateClient->contacts()->create([
            'name' => 'منى سعيد', 'position' => 'المدير المالي', 'phone' => null, 'is_primary' => true,
        ]);

        $client->powersOfAttorney()->create([
            'number' => '2024/ح/1234',
            'notary_office' => 'الشهر العقاري - مدينة نصر',
            'issued_at' => now()->subMonths(6),
            'expires_at' => now()->addYears(2),
            'lawyer_ids' => [$lawyer?->id],
            'scope' => 'توكيل عام في القضايا',
        ]);

        $opponent = Opponent::create(['name' => 'شركة الأمل للمقاولات', 'type' => 'company', 'lawyer_name' => 'محمد فتحي']);

        $case = LegalCase::create([
            'title' => 'مطالبة بقيمة أعمال مقاولات',
            'case_number' => '1520',
            'case_year' => now()->year,
            'client_id' => $client->id,
            'client_role' => 'plaintiff',
            'case_type_id' => CaseType::where('name', 'تجاري')->value('id'),
            'court_id' => Court::where('name', 'محكمة القاهرة الاقتصادية')->value('id'),
            'circuit' => 'الدائرة 5 تجاري',
            'lead_lawyer_id' => $lawyer?->id,
            'status' => CaseStatus::IN_PROGRESS,
            'subject' => 'المطالبة بمبلغ 850,000 جنيه قيمة أعمال منفذة',
            'claim_amount' => 850000,
            'filed_at' => now()->subMonths(2),
            'created_by' => $admin?->id,
        ]);
        $case->lawyers()->sync(array_filter([$lawyer?->id, $admin?->id]));
        $case->opponents()->attach($opponent->id, ['role' => 'defendant']);

        $case->hearings()->createMany([
            ['scheduled_at' => now()->subWeeks(3)->setTime(10, 0), 'type' => 'pleading', 'status' => HearingStatus::HELD, 'lawyer_id' => $lawyer?->id, 'outcome' => 'تأجلت الدعوى لتقديم المستندات', 'next_hearing_at' => now()->addDays(3)],
            ['scheduled_at' => now()->addDays(3)->setTime(10, 0), 'type' => 'evidence', 'status' => HearingStatus::SCHEDULED, 'lawyer_id' => $lawyer?->id, 'requirements' => 'إحضار أصل العقد وكشف الحساب'],
        ]);

        $case->activities()->create([
            'user_id' => $lawyer?->id, 'type' => 'memo', 'title' => 'إعداد مذكرة بالدفاع', 'occurred_at' => now()->subDays(5), 'hours_spent' => 4,
        ]);

        $family = LegalCase::create([
            'title' => 'دعوى نفقة',
            'case_number' => '330',
            'case_year' => now()->year,
            'client_id' => $client->id,
            'client_role' => 'defendant',
            'case_type_id' => CaseType::where('name', 'أحوال شخصية')->value('id'),
            'court_id' => Court::where('name', 'محكمة الأسرة - مصر الجديدة')->value('id'),
            'lead_lawyer_id' => $lawyer?->id,
            'status' => CaseStatus::OPEN,
            'filed_at' => now()->subWeeks(2),
            'created_by' => $admin?->id,
        ]);
        $family->hearings()->create(['scheduled_at' => now()->addDays(10)->setTime(9, 30), 'status' => HearingStatus::SCHEDULED, 'lawyer_id' => $lawyer?->id]);

        $company = Company::create([
            'client_id' => $corporateClient->id,
            'name' => 'شركة النيل للتجارة ذ.م.م',
            'name_en' => 'Nile Trading LLC',
            'legal_form' => LegalForm::LLC,
            'status' => CompanyStatus::ACTIVE,
            'issued_capital' => 500000,
            'paid_capital' => 500000,
            'commercial_register_no' => '123456',
            'commercial_register_office' => 'القاهرة',
            'commercial_register_expires_at' => now()->addMonths(2),
            'tax_card_no' => '987-654-321',
            'gafi_file_no' => 'GAFI-2020-4455',
            'incorporated_at' => now()->subYears(4),
            'law' => 'قانون 159 لسنة 1981',
            'activity' => 'تجارة وتوزيع المواد الغذائية',
            'governorate' => 'القاهرة',
            'responsible_lawyer_id' => $lawyer?->id,
        ]);

        $company->partners()->createMany([
            ['name' => 'عمر سالم', 'nationality' => 'مصري', 'id_number' => null, 'role' => PartnerRole::MANAGING_PARTNER, 'share_percentage' => 60, 'is_signatory' => true],
            ['name' => 'هدى إبراهيم', 'nationality' => 'مصرية', 'id_number' => null, 'role' => PartnerRole::PARTNER, 'share_percentage' => 40],
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
            'description' => 'زيادة رأس المال المصدر إلى 1,000,000 جنيه',
            'checklist' => [
                ['item' => 'محضر الجمعية العامة غير العادية', 'done' => true],
                ['item' => 'شهادة إيداع رأس المال', 'done' => true],
                ['item' => 'تعديل عقد الشركة', 'done' => false],
                ['item' => 'التأشير بالسجل التجاري', 'done' => false],
            ],
        ]);

        CompanyDeadline::create([
            'company_id' => $company->id,
            'type' => DeadlineType::COMMERCIAL_REGISTER,
            'title' => 'تجديد السجل التجاري',
            'due_at' => now()->addMonths(2),
            'recurring_yearly' => true,
        ]);

        $client->feeAgreements()->create([
            'legal_case_id' => $case->id, 'type' => 'installments', 'total_amount' => 60000,
            'advance_amount' => 20000, 'installments_count' => 4, 'agreed_at' => now()->subMonths(2),
        ]);

        $invoice = Invoice::create([
            'client_id' => $client->id,
            'legal_case_id' => $case->id,
            'status' => InvoiceStatus::SENT,
            'issued_at' => now()->subMonth(),
            'due_at' => now()->addWeeks(2),
        ]);
        InvoiceItem::create(['invoice_id' => $invoice->id, 'description' => 'الدفعة المقدمة - أتعاب القضية', 'quantity' => 1, 'unit_price' => 20000]);
        InvoiceItem::create(['invoice_id' => $invoice->id, 'description' => 'رسوم قضائية', 'quantity' => 1, 'unit_price' => 2500]);
        Payment::create(['client_id' => $client->id, 'invoice_id' => $invoice->id, 'amount' => 10000, 'method' => 'bank_transfer', 'paid_at' => now()->subWeeks(2), 'received_by' => $admin?->id]);

        $case->expenses()->create(['category' => 'court_fees', 'description' => 'رسوم رفع الدعوى', 'amount' => 2500, 'spent_at' => now()->subMonths(2), 'paid_by' => $admin?->id]);

        Task::create([
            'title' => 'تجهيز حافظة المستندات للجلسة القادمة',
            'taskable_type' => LegalCase::class, 'taskable_id' => $case->id,
            'assigned_to' => $lawyer?->id, 'created_by' => $admin?->id,
            'priority' => 'high', 'due_at' => now()->addDays(2),
        ]);
    }
}
