<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment(['local', 'testing'])) {
            $demoPassword = env('DEMO_PASSWORD', 'password');

            $users = [
                ['email' => 'admin@lawfirm.test', 'name' => 'مدير النظام', 'password' => $demoPassword, 'job_title' => 'المحامي المدير', 'locale' => 'ar', 'role' => 'admin'],
                ['email' => 'lawyer@lawfirm.test', 'name' => 'أحمد محمود', 'password' => $demoPassword, 'job_title' => 'محامٍ بالاستئناف', 'bar_registration_no' => 'DEMO-12345', 'role' => 'lawyer'],
                ['email' => 'secretary@lawfirm.test', 'name' => 'سارة علي', 'password' => $demoPassword, 'job_title' => 'سكرتارية', 'role' => 'secretary'],
                ['email' => 'accountant@lawfirm.test', 'name' => 'محمد حسن', 'password' => $demoPassword, 'job_title' => 'محاسب', 'role' => 'accountant'],
            ];

            foreach ($users as $data) {
                $role = $data['role'];
                unset($data['role']);

                $user = User::updateOrCreate(['email' => $data['email']], $data);
                $user->syncRoles($role);
            }

            return;
        }

        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');

        if (! $email || ! $password) {
            $this->command?->warn('ADMIN_EMAIL and ADMIN_PASSWORD are required to seed a production admin user.');

            return;
        }

        $admin = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => env('ADMIN_NAME', 'System Administrator'),
                'password' => $password,
                'job_title' => env('ADMIN_JOB_TITLE', 'Administrator'),
                'locale' => env('APP_LOCALE', 'ar'),
                'is_active' => true,
            ]
        );

        $admin->syncRoles('admin');
    }
}
