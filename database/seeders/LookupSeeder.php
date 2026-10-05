<?php

namespace Database\Seeders;

use App\Models\CaseType;
use App\Models\Court;
use Illuminate\Database\Seeder;

class LookupSeeder extends Seeder
{
    public function run(): void
    {
        $caseTypes = [
            ['name' => 'مدني', 'name_en' => 'Civil', 'color' => 'info'],
            ['name' => 'جنائي', 'name_en' => 'Criminal', 'color' => 'danger'],
            ['name' => 'تجاري', 'name_en' => 'Commercial', 'color' => 'primary'],
            ['name' => 'أحوال شخصية', 'name_en' => 'Family / Personal Status', 'color' => 'warning'],
            ['name' => 'عمالي', 'name_en' => 'Labor', 'color' => 'success'],
            ['name' => 'إداري', 'name_en' => 'Administrative', 'color' => 'gray'],
            ['name' => 'اقتصادي', 'name_en' => 'Economic', 'color' => 'primary'],
            ['name' => 'إيجارات', 'name_en' => 'Tenancy', 'color' => 'info'],
            ['name' => 'تنفيذ', 'name_en' => 'Execution', 'color' => 'gray'],
            ['name' => 'تحكيم', 'name_en' => 'Arbitration', 'color' => 'warning'],
        ];

        foreach ($caseTypes as $type) {
            CaseType::firstOrCreate(['name' => $type['name']], $type);
        }

        $courts = [
            ['name' => 'محكمة القاهرة الاقتصادية', 'name_en' => 'Cairo Economic Court', 'type' => 'economic', 'city' => 'القاهرة'],
            ['name' => 'محكمة شمال القاهرة الابتدائية', 'name_en' => 'North Cairo Court of First Instance', 'type' => 'first_instance', 'city' => 'القاهرة'],
            ['name' => 'محكمة جنوب القاهرة الابتدائية', 'name_en' => 'South Cairo Court of First Instance', 'type' => 'first_instance', 'city' => 'القاهرة'],
            ['name' => 'محكمة الجيزة الابتدائية', 'name_en' => 'Giza Court of First Instance', 'type' => 'first_instance', 'city' => 'الجيزة'],
            ['name' => 'محكمة استئناف القاهرة', 'name_en' => 'Cairo Court of Appeal', 'type' => 'appeal', 'city' => 'القاهرة'],
            ['name' => 'محكمة النقض', 'name_en' => 'Court of Cassation', 'type' => 'cassation', 'city' => 'القاهرة'],
            ['name' => 'محكمة القضاء الإداري', 'name_en' => 'Administrative Judiciary Court', 'type' => 'administrative', 'city' => 'القاهرة'],
            ['name' => 'محكمة الأسرة - مصر الجديدة', 'name_en' => 'Family Court - Heliopolis', 'type' => 'family', 'city' => 'القاهرة'],
            ['name' => 'محكمة جنح مدينة نصر', 'name_en' => 'Nasr City Misdemeanour Court', 'type' => 'criminal', 'city' => 'القاهرة'],
            ['name' => 'محكمة الإسكندرية الابتدائية', 'name_en' => 'Alexandria Court of First Instance', 'type' => 'first_instance', 'city' => 'الإسكندرية'],
        ];

        foreach ($courts as $court) {
            Court::firstOrCreate(['name' => $court['name']], $court);
        }
    }
}
