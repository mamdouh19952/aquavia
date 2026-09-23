# دليل مشروع Aquavia Pools — شرح كامل خطوة بخطوة

هذا الملف بيشرح المشروع بالكامل: هو مبني إزاي، كل جزء بيعمل إيه، وإزاي تعدّل فيه من غير ما تكسر حاجة. الكود بسيط ومباشر عن قصد، وده موضح في [CLAUDE.md](CLAUDE.md) اللي بيحدد قواعد المشروع.

> **تم اختباره فعليًا:** شغّلت المشروع على المتصفح، سجّلت دخول كـ Admin، وضفت مشروع تجريبي في "أعمالنا" برفع صورتين، وأكدت إنه ظهر صح في لوحة التحكم وفي الموقع العام. الفلو شغال 100%.

---

## 1. الفكرة العامة

موقع تعريفي (Marketing Site) لشركة **Aquavia Pools** (تصميم/تنفيذ/صيانة حمامات سباحة)، ثنائي اللغة (عربي/إنجليزي)، كله صفحات ثابتة **ما عدا** قسم واحد ديناميكي: **"أعمالنا" (Our Work)** — بورتفوليو المشاريع، وده بس اللي بيتدار من لوحة تحكم بسيطة (Admin واحد فقط، بدون تسجيل مستخدمين عاديين).

## 2. الأدوات المستخدمة

| الجزء | الأداة |
|---|---|
| Backend | Laravel 12 + PHP 8.2+ |
| قاعدة البيانات | SQLite (تطوير) / MySQL (إنتاج) |
| الواجهة | Blade + Bootstrap 5 (+ نسخة RTL للعربي) |
| الصور | `spatie/laravel-medialibrary` ملفوفة جوه `App\Services\MediaService` |
| JavaScript | Vanilla JS فقط (بدون React/Vue) |
| الإشعارات | Toastr |
| الـ Build | Vite |

---

## 3. خريطة الملفات (وظيفة كل حاجة)

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── PageController.php          ← الصفحات الثابتة (Home/About/Services/Contact)
│   │   ├── LocaleController.php        ← تبديل اللغة (عربي/إنجليزي)
│   │   ├── WorkController.php          ← عرض "أعمالنا" للزوار (عام، بدون دخول)
│   │   ├── Auth/AuthController.php     ← تسجيل دخول/خروج الأدمن فقط
│   │   ├── ProfileController.php       ← تعديل بيانات/باسورد الأدمن
│   │   └── Admin/
│   │       ├── DashboardController.php ← لوحة تحكم الأدمن الرئيسية
│   │       └── WorkController.php      ← CRUD كامل لمشاريع "أعمالنا"
│   ├── Middleware/
│   │   ├── SetLocale.php               ← بيحدد اللغة الحالية من الـ session
│   │   └── EnsureUserIsAdmin.php       ← بيمنع أي حد مش أدمن من /admin/*
│   └── Requests/Admin/
│       ├── StoreWorkProjectRequest.php ← قواعد التحقق عند إضافة مشروع
│       └── UpdateWorkProjectRequest.php← قواعد التحقق عند تعديل مشروع
├── Models/
│   ├── User.php                        ← فيه is_admin (بدون Roles/Policies، أدمن واحد بس)
│   └── WorkProject.php                 ← موديل مشروع "أعمالنا" + الجاليري
└── Services/
    └── MediaService.php                ← الوسيط الوحيد اللي بيكلم Spatie

database/
├── migrations/                         ← تعريف الجداول (users, work_projects, media, ...)
└── seeders/
    ├── AdminUserSeeder.php             ← بيعمل حساب الأدمن الافتراضي
    └── WorkProjectSeeder.php           ← بيانات تجريبية لـ "أعمالنا"

lang/
├── ar/site.php                         ← كل النصوص الثابتة بالعربي
└── en/site.php                         ← نفس النصوص بالإنجليزي

resources/views/
├── layouts/
│   ├── public.blade.php                ← layout الصفحات العامة (navbar+footer+واتساب)
│   ├── admin.blade.php                 ← layout لوحة التحكم (sidebar)
│   └── guest.blade.php                 ← layout صفحة تسجيل الدخول
├── partials/
│   ├── navbar.blade.php, footer.blade.php
│   ├── whatsapp-button.blade.php       ← الزر العائم بتاع واتساب
│   └── toast.blade.php                 ← رسائل النجاح/الخطأ
├── components/                         ← x-input, x-button, x-alert, x-loader
├── public/                             ← Home, About, Services, Contact + work/index + work/show
└── admin/work/                         ← index (جدول المشاريع), create, edit

routes/web.php                          ← كل الراوتس (لا يوجد api.php مُفعّل)
```

---

## 4. إزاي اتبنى المشروع (بالترتيب الفعلي)

### الخطوة 1 — الأساس (Auth للأدمن فقط)
- إضافة عمود `is_admin` لجدول `users` (migration منفصلة).
- `AuthController` بسيط: `showLogin`, `login`, `logout` — بدون تسجيل حسابات جديدة (لا يوجد Register).
- Middleware `EnsureUserIsAdmin` (مسجّل باسم `admin` في `bootstrap/app.php`) بيحمي كل روابط `/admin/*`.
- `AdminUserSeeder` بيعمل حساب أدمن جاهز: `admin@aquaviapools.com` / `password`.

### الخطوة 2 — تعدد اللغات (AR/EN)
- Middleware `SetLocale` بيقرأ اللغة من الـ session (الافتراضي `ar`)، ولو مش `ar` أو `en` يرجّعها `ar`.
- `LocaleController@switch` هو اللي بيغيّر اللغة (رابط `/lang/{locale}`) ويرجّع المستخدم لنفس الصفحة.
- كل النصوص الثابتة (عناوين، أزرار، لابلز) متخزنة في `lang/ar/site.php` و `lang/en/site.php` وبتتنادى في الـ Blade بـ `{{ __('site.key') }}`.
- Bootstrap RTL (`resources/css/app-rtl.css`) بيتحمّل تلقائيًا لما اللغة عربي.

### الخطوة 3 — الـ Layouts
- 3 layouts منفصلة حسب الاستخدام: `public` (الموقع العام)، `admin` (لوحة التحكم)، `guest` (صفحة الدخول). كل واحد فيه navbar/footer مناسب، وبيشيل `<x-loader />` وسكريبتات Toastr مرة واحدة بس.

### الخطوة 4 — الصفحات الثابتة
- `PageController` بيرجّع 4 Views بسيطة (`public.home`, `public.about`, `public.services`, `public.contact`) — كلها محتوى ثابت من ملفات الترجمة، مفيش أي داتابيز هنا.

### الخطوة 5 — قسم "أعمالنا" (الجزء الديناميكي الوحيد)
1. **Migration** `work_projects`: `title_ar`, `title_en`, `description_ar`, `description_en`, `location`.
2. **Model** `WorkProject` — بيعمل `implements HasMedia` + `use InteractsWithMedia`، وعامل `addMediaCollection('gallery')`. فيه دالتين مساعدتين `title()` و `description()` بيرجّعوا النص حسب اللغة الحالية تلقائيًا.
3. **MediaService** — الوسيط الوحيد لرفع/حذف الصور (`upload`, `update`, `deleteMedia`, `deleteMediaItem`). الـ Controllers ما بتكلمش Spatie مباشرة أبدًا.
4. **Form Requests** (`StoreWorkProjectRequest` / `UpdateWorkProjectRequest`) — التحقق: العنوان والوصف والموقع مطلوبين بالعربي والإنجليزي، والصور لازم تكون `image` وحجمها أقصى 8 ميجا.
5. **Admin Controller** (`Admin\WorkController`) — CRUD كامل: `index`, `create`, `store`, `edit`, `update`, `destroy`. عند الحفظ بيلف على كل صورة مرفوعة ويبعتها لـ `MediaService::upload()`. عند التعديل ممكن كمان تمسح صور قديمة (`remove_media[]`) وتضيف صور جديدة في نفس الوقت.
6. **Public Controller** (`WorkController`) — `index` بيعرض كل المشاريع، `show` بيعرض مشروع واحد بكل صوره، ولو الـ ID مش موجود بيرجّع صفحة "not found" مخصصة بدل خطأ 500.
7. **Views** — جدول بسيط في الأدمن، وصفحة كروت + صفحة تفاصيل في الموقع العام.

### الخطوة 6 — لمسات إضافية
- زرار واتساب عائم (`partials/whatsapp-button.blade.php`) ظاهر في كل صفحات الموقع العام، الرقم بييجي من `.env` (`COMPANY_WHATSAPP_NUMBER`).
- الألوان (نيفي + فيروزي) متعرّفة كـ CSS variables في `resources/css/app.css` (`--brand-navy`, `--brand-turquoise`) وبتتستخدم في كل مكان بدل ما تتكرر.

---

## 5. تشغيل المشروع محليًا

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate

# القاعدة الافتراضية SQLite — تأكد إن DB_CONNECTION=sqlite في .env
# (لو عايز MySQL بدل كده، غيّرها وشغّل MySQL على جهازك الأول)
php artisan migrate
php artisan db:seed --class=AdminUserSeeder
php artisan storage:link

npm run build     # أو npm run dev للتطوير مع hot-reload
php artisan serve
```

بيانات دخول الأدمن الافتراضية: `admin@aquaviapools.com` / `password` (غيّرها بعد أول دخول من صفحة Profile).

---

## 6. إزاي تعدّل حاجات شائعة

### أ) تغيير نص ثابت في الموقع (عربي/إنجليزي)
كل النصوص في `lang/ar/site.php` و `lang/en/site.php`. غيّر القيمة في الملفين (نفس الـ key)، ومفيش داعي تلمس أي Blade file.

```php
// lang/ar/site.php
'hero_title' => 'العنوان الجديد هنا',
```

### ب) تغيير الألوان / الشعار
الألوان الأساسية في [resources/css/app.css](resources/css/app.css) أعلى الملف:
```css
:root {
    --brand-navy: #0a1f44;
    --brand-turquoise: #2fd4e0;
}
```
غيّر القيم دي وكل الموقع هيتغيّر لونه تلقائيًا (الأزرار، الروابط، الـ navbar). بعد أي تعديل CSS لازم تعمل:
```bash
npm run build
```

### ج) تغيير رقم واتساب / تليفون / إيميل الشركة
كلها متغيرات بيئة في `.env`:
```
COMPANY_WHATSAPP_NUMBER=201000000000
COMPANY_PHONE="+20 100 000 0000"
COMPANY_EMAIL=info@aquaviapools.com
COMPANY_ADDRESS="Cairo, Egypt"
```
غيّرها وأعد تشغيل السيرفر (`php artisan config:clear` لو الكاش شغال).

### د) إضافة/حذف صور لمشروع في "أعمالنا"
ده جاهز بالفعل ومختبر:
- **إضافة مشروع جديد بصور:** `/admin/work/create` → املأ البيانات → اختار كذا صورة من حقل "Photos" (بيقبل اختيار متعدد) → Save.
- **إضافة صور لمشروع موجود:** افتح `/admin/work/{id}/edit` → حقل "Add More Photos" → اختار الصور الجديدة → Update.
- **حذف صورة معينة:** في نفس صفحة التعديل، تحت كل صورة فيه checkbox "Remove" — علّمه واحفظ.
- **حذف كل صور مشروع تلقائيًا:** بيحصل لوحده لما تمسح المشروع كله (زرار Delete في `/admin/work`).

أول صورة بترفعها بتبقى الـ **thumbnail** تلقائيًا (`WorkProject::thumbnail()`) — يعني ترتيب الرفع بيهم.

### هـ) إضافة حقل جديد لمشروع "أعمالنا" (مثال: مدة التنفيذ "duration")
مثال كامل خطوة بخطوة لو حبيت تضيف حقل جديد زي "مدة التنفيذ":

1. **Migration جديدة:**
```bash
php artisan make:migration add_duration_to_work_projects_table --table=work_projects
```
```php
public function up(): void
{
    Schema::table('work_projects', function (Blueprint $table) {
        $table->string('duration')->nullable()->after('location');
    });
}
```
```bash
php artisan migrate
```

2. **الموديل** `app/Models/WorkProject.php` — ضيف `duration` لمصفوفة `$fillable`.

3. **الـ Form Requests** — ضيف قاعدة تحقق في الملفين:
```php
'duration' => ['nullable', 'string', 'max:255'],
```

4. **الـ Views** — ضيف حقل input في `admin/work/create.blade.php` و `admin/work/edit.blade.php`:
```blade
<x-input name="duration" label="Duration" :value="$project->duration ?? ''" />
```

5. **عرضه للزوار** — ضيفه في `public/work/show.blade.php` جنب باقي بيانات المشروع.

نفس الخطوات بالظبط تنفع لأي حقل جديد (سعر، مساحة، نوع الحمام، ...إلخ).

### و) إضافة صفحة ثابتة جديدة (مثال: صفحة "الأسئلة الشائعة")
1. ضيف method جديدة في `PageController`:
```php
public function faq(): View
{
    return view('public.faq');
}
```
2. ضيف route في `routes/web.php`:
```php
Route::get('/faq', [PageController::class, 'faq'])->name('faq');
```
3. اعمل View جديد `resources/views/public/faq.blade.php`:
```blade
@extends('layouts.public')
@section('content')
    <h1>{{ __('site.faq_title') }}</h1>
@endsection
```
4. ضيف النصوص المطلوبة في `lang/ar/site.php` و `lang/en/site.php`، وضيف رابط الصفحة في `partials/navbar.blade.php`.

### ز) تغيير باسورد/بيانات الأدمن
من الموقع نفسه: سجّل دخول → `/profile` → عدّل الاسم/الإيميل/الباسورد. مفيش داعي تروح للداتابيز يدويًا.

### ح) النشر (Deployment) على سيرفر حقيقي
- غيّر `.env` على السيرفر لـ `DB_CONNECTION=mysql` ببيانات قاعدة البيانات الحقيقية (زي ما مكتوب في CLAUDE.md، MySQL هو المخطط للإنتاج).
- شغّل: `composer install --no-dev`, `npm run build`, `php artisan migrate --force`, `php artisan storage:link`.
- تأكد إن `APP_DEBUG=false` و `APP_ENV=production`.

---

## 7. قواعد مهمة لازم تفضل ماشي عليها (من CLAUDE.md)

- **الصور:** أي رفع/حذف صورة لازم يعدّي من `MediaService` بس — ممنوع نداء Spatie مباشرة من أي Controller.
- **مفيش Service layer عام:** المنطق بسيط وبيتكتب جوه الـ Controller مباشرة، إلا لو اتكرر في أكتر من Controller.
- **مفيش AJAX افتراضيًا:** الفورمات بتبعت عادي (POST/PUT) وترجع redirect + Toastr، مش fetch/JSON، إلا لو محتاج فعلاً تجربة بدون reload.
- **الـ Admin واحد بس:** مفيش تسجيل حسابات عامة، ومفيش Policies لأن مفيش ownership لأكتر من مستخدم.
- **التحقق دايمًا Server-side** عبر Form Requests، وعرض الأخطاء بـ `@error()` القياسية.

---

## 8. ملاحظة عن الاختبار اللي اتعمل

أثناء التأكد من الفلو، اتعمل:
1. تشغيل السيرفر محليًا (`php artisan serve` على SQLite).
2. تسجيل دخول بحساب الأدمن الافتراضي.
3. إضافة مشروع تجريبي "Villa Pool - Sheikh Zayed" مع رفع صورتين حقيقيتين.
4. التأكد إن المشروع ظهر في `/admin/work` وفي `/our-work` العامة، وإن الصور بتترفع من `/storage/...` بنجاح (200 OK).

المشروع التجريبي ده موجود دلوقتي في الداتابيز — سيبته كمثال حي تقدر تفتحه من `/admin/work`، وممكن تمسحه في أي وقت من نفس الصفحة لو مش محتاجه.
