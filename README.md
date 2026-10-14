# نظام إدارة مكتب المحاماة — Law Firm Management System

Laravel 12 · Filament 5 · Livewire 4 · PHP 8.2+ · عربي / English (RTL)

## الموديولات

| الموديول | ما يشمله |
|---|---|
| **الموكلون** | بيانات الموكلين (أفراد / شركات)، جهات الاتصال، التوكيلات مع تاريخ انتهائها، الخصوم ومحاميهم |
| **القضايا والجلسات** | ملف القضية (المحكمة، الدائرة، الدرجة، فريق العمل، الخصوم)، الجلسات مع تسجيل ما تم وإنشاء الجلسة القادمة تلقائياً، سجل الإجراءات وساعات العمل، أجندة الجلسات (اليوم / الأسبوع / جلساتي)، تذكير يومي بجلسات الغد |
| **المستندات والعقود** | أرشيف مركزي مرتبط بالقضية / الموكل / الشركة / العقد، تصنيفات، تحكم في ظهور المستند للموكل، سجل العقود بتواريخ الانتهاء |
| **الأتعاب والفواتير** | اتفاقيات الأتعاب (ثابتة / ساعة / نسبة / أقساط)، فواتير ببنود وضريبة وخصم، تسجيل دفعات وإيصالات، مصروفات القضايا، طباعة الفاتورة والإيصال، تحويل الفواتير المتأخرة تلقائياً |
| **الشركات وهيئة الاستثمار** | ملف الشركة (الشكل القانوني، رأس المال، السجل التجاري، البطاقة الضريبية، رقم ملف الهيئة)، الشركاء والمساهمون، إجراءات الهيئة (تأسيس / زيادة رأس مال / تعديل...) مع قائمة مستندات جاهزة لكل إجراء، الاستحقاقات السنوية (تجديد السجل، الجمعية العمومية...) مع تذكير قبلها بـ 30 يوم |
| **المهام** | مهام مرتبطة بقضية / شركة / إجراء، أولوية، مواعيد، لوحة "مهامي" |
| **بوابة الموكلين** | `/portal` — الموكل يتابع قضاياه، الجلسات، المستندات المصرح بها، الفواتير والمتبقي، شركاته وإجراءاتها |
| **الصلاحيات** | أدوار جاهزة: مدير / محامٍ / سكرتارية / محاسب، وصلاحيات تفصيلية لكل مورد قابلة للتعديل من الواجهة |

## المتطلبات

- PHP 8.2+ مع امتدادات: `pdo_mysql` (أو `pdo_sqlite`)، `mbstring`، `intl`، `gd`، `zip`، `fileinfo`
- Composer 2
- MySQL 8 / MariaDB 10.6+ (أو SQLite للتجربة)
- Node 18+ (اختياري — مطلوب فقط لو هتعدل ثيم Filament)

## التثبيت

```bash
composer install
cp .env.example .env
php artisan key:generate

# عدّل بيانات قاعدة البيانات في .env (MySQL)، أو للتجربة السريعة بـ SQLite:
#   DB_CONNECTION=sqlite   ثم   touch database/database.sqlite
php artisan migrate --seed
php artisan storage:link

php artisan serve
```

> **ملاحظة:** `composer install` بيشغّل `filament:upgrade` تلقائياً بعد التحميل. لو ظهرت مشكلة في الأصول (assets) شغّل `php artisan filament:assets`.

### حسابات التجربة (بعد الـ seed في بيئة `local`)

| الدور | البريد | كلمة المرور | الرابط |
|---|---|---|---|
| مدير النظام | admin@lawfirm.test | password | /admin |
| محامٍ | lawyer@lawfirm.test | password | /admin |
| سكرتارية | secretary@lawfirm.test | password | /admin |
| محاسب | accountant@lawfirm.test | password | /admin |
| موكل (بوابة) | client@lawfirm.test | password | /portal |

بيانات تجريبية (موكلين، قضيتين، جلسات، شركة، إجراء زيادة رأس مال، فاتورة، دفعة) بتتضاف تلقائياً في بيئة `local` فقط.

## التشغيل في الإنتاج

```bash
# المهام المجدولة (تذكير الجلسات 08:00، الاستحقاقات 08:15، الفواتير المتأخرة 00:30)
* * * * * cd /path/to/lawfirm && php artisan schedule:run >> /dev/null 2>&1

# طابور الإشعارات
php artisan queue:work --tries=3
```

أوامر يدوية مفيدة:

```bash
php artisan firm:remind-hearings --days=1
php artisan firm:remind-deadlines --days=30
php artisan firm:mark-overdue-invoices
```

إعدادات المكتب في `.env`:

```
FIRM_NAME="مكتب فلان للمحاماة"
FIRM_CURRENCY=EGP
FIRM_HEARING_REMINDER_DAYS=1
FIRM_DEADLINE_REMINDER_DAYS=30
```

## اللغة

اللغة الافتراضية عربي (RTL). تبديل اللغة من قائمة المستخدم أعلى الصفحة، ويُحفظ الاختيار في الجلسة وفي ملف المستخدم. الترجمات في `lang/ar` و `lang/en`.

## الاختبارات

```bash
php artisan test
```

تغطي: احتساب الفواتير والحالات، الأرقام المرجعية، صلاحيات الأدوار، فصل بيانات الموكلين في البوابة، تذكيرات الجلسات، الوصول للوحات.

## هيكل الكود

```
app/
├── Enums/                 # حالات وأنواع مترجمة (HasLabel / HasColor)
├── Models/                # Eloquent + GeneratesReference trait
├── Policies/              # BasePolicy → صلاحيات Spatie لكل مورد
├── Filament/Admin/        # لوحة المكتب: Resources / Pages / Widgets / RelationManagers
├── Filament/Portal/       # بوابة الموكلين (قراءة فقط ومحدودة بالموكل)
├── Console/Commands/      # التذكيرات والفواتير المتأخرة
├── Notifications/         # إشعارات قاعدة بيانات + بريد
└── Support/Permissions.php # مصفوفة الأدوار والصلاحيات
```

## أفكار للمرحلة التالية

- تصدير الفاتورة PDF (`barryvdh/laravel-dompdf`)
- تقويم الجلسات (FullCalendar plugin لـ Filament)
- إشعارات واتساب/SMS للموكلين
- استيراد القضايا من Excel
- قوالب عقود ومذكرات
