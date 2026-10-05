<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@lawfirm.test'],
            ['name' => 'مدير النظام', 'password' => 'password', 'job_title' => 'المحامي المدير', 'locale' => 'ar']
        );
        $admin->syncRoles('admin');

        $lawyer = User::firstOrCreate(
            ['email' => 'lawyer@lawfirm.test'],
            ['name' => 'أحمد محمود', 'password' => 'password', 'job_title' => 'محامٍ بالاستئناف', 'bar_registration_no' => '12345']
        );
        $lawyer->syncRoles('lawyer');

        $secretary = User::firstOrCreate(
            ['email' => 'secretary@lawfirm.test'],
            ['name' => 'سارة علي', 'password' => 'password', 'job_title' => 'سكرتارية']
        );
        $secretary->syncRoles('secretary');

        $accountant = User::firstOrCreate(
            ['email' => 'accountant@lawfirm.test'],
            ['name' => 'محمد حسن', 'password' => 'password', 'job_title' => 'محاسب']
        );
        $accountant->syncRoles('accountant');
    }
}
