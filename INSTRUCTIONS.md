# التعليمات الكاملة — Layout, Toastr, Loader, و CRUD + Search

## 📋 الفهرس
1. [Layout Pattern المستخدم في المشروع](#layout-pattern-المستخدم-في-المشروع)
2. [استخدام Toastr Notifications](#استخدام-toastr-notifications)
3. [استخدام الـ Loader](#استخدام-الـ-loader)
4. [تجنب الـ Reload غير الضروري](#تجنب-الـ-reload-غير-الضروري)
5. [مثال كامل: CRUD + Search بدون Reload](#مثال-كامل-crud--search-بدون-reload)
6. [رفع الصور مع Preview (MediaService)](#رفع-الصور-مع-preview-mediaservice)
7. [Pagination بـ Bootstrap](#pagination-بـ-bootstrap)

---

## Layout Pattern المستخدم في المشروع

الفكرة: **Layout ثابت (navbar + footer) + Content اللي بيتغير بس**، زي ما إنت متعود.

في المشروع 4 layouts جاهزة، كل واحد لحالة مختلفة:

| الملف | الاستخدام |
|------|-----------|
| `layouts/app.blade.php` | صفحات المستخدم المسجل (Dashboard, Profile) — navbar بسيط + dropdown |
| `layouts/admin.blade.php` | لوحة تحكم الأدمن — sidebar + محتوى |
| `layouts/public.blade.php` | صفحات عامة (Home) — navbar عادي + footer |
| `layouts/guest.blade.php` | صفحات الدخول (Login, Register) — بدون navbar |

كل الـ layouts فيها نفس التركيب:
```
navbar (partials/navbar أو inline)
    ↓
@yield('content') ← هنا بس بتتغير الصفحات
    ↓
footer
    ↓
<x-loader />  ← موجود مرة واحدة، مش محتاج تكرره في كل صفحة
scripts (Bootstrap JS + Toastr + showLoader/hideLoader helpers)
```

### استخدام الـ Layout في صفحة جديدة:
```blade
@extends('layouts.app')

@section('title', 'Products')

@section('content')
    <h1>All Products</h1>
    <!-- المحتوى بتاعك هنا بس -->
@endsection
```

**مهم:** الـ `<x-loader />` والـ scripts موجودين مرة واحدة جوه كل layout. مش محتاج تحطهم تاني في الصفحات — بس استخدم `showLoader()` / `hideLoader()` في أي مكان.

---

## استخدام Toastr Notifications

### 1. في الـ Controller — أي redirect ممكن يجيب معاه رسالة:

```php
// ✅ نجاح
return redirect()->route('products.index')->with('success', 'تم إنشاء المنتج بنجاح!');

// ❌ خطأ
return redirect()->back()->with('error', 'حدث خطأ أثناء الحفظ.');

// ⚠️ تحذير
return redirect()->back()->with('warning', 'المنتج تم أرشفته.');

// ℹ️ معلومة
return redirect()->back()->with('info', 'لا توجد تغييرات جديدة.');
```

هذا كل اللي محتاجه — `resources/views/partials/toast.blade.php` بيقرأ الـ session تلقائياً ويطلّع Toastr، وهو موجود جوه كل layout.

### 2. في AJAX/fetch (بدون redirect):

```js
toastr.success('تم الحذف بنجاح!');
toastr.error('حدث خطأ!');
toastr.warning('تحذير...');
toastr.info('معلومة...');
```

---

## استخدام الـ Loader

الـ Loader (`resources/views/components/loader.blade.php`) عبارة عن overlay بيغطي الشاشة، وهو **موجود تلقائياً في كل layout** (`<x-loader />`)، مفيش داعي تضيفه في الصفحات.

### الاستخدام:

**تلقائي مع أي `<form>` عادي:**
كل form submit عادي بيظهر الـ Loader تلقائياً (مسجل في `partials/scripts.blade.php` على حدث `submit`). لو مش عايز الـ Loader يظهر لفورم معين (زي فورم الـ logout السريع)، ضيف `data-no-loader`:

```blade
<form method="POST" action="{{ route('logout') }}" data-no-loader>
    @csrf
    <button type="submit">Logout</button>
</form>
```

**يدوي مع fetch/AJAX:**
```js
showLoader();

fetch('/tasks', { method: 'POST', /* ... */ })
    .then(res => res.json())
    .then(data => {
        hideLoader();
        toastr.success('تم!');
    })
    .catch(() => {
        hideLoader();
        toastr.error('خطأ!');
    });
```

---

## تجنب الـ Reload غير الضروري

### الوضع الافتراضي (موصى به):
Form عادي → Route → Controller → Database → Redirect → Toastr. ده بيعمل reload كامل للصفحة، وهو الأنسب لمعظم الحالات (بسيط، آمن، مفيهوش تعقيد).

### لو عايز تمنع الـ Reload (اختياري، لما فعلاً محتاجه):
استخدم `fetch()` بدل الـ form submit العادي، وارجع JSON من الـ Controller بدل `redirect()`.

**القاعدة الذهبية:**
- الصفحة تتحمل أول مرة **Server-rendered** عادي (فيها بيانات جاهزة من الـ Controller).
- أي عملية بعد كده (بحث، حذف، إضافة، تعديل) تتم عبر `fetch()` وترجع JSON، والـ JavaScript يعدّل الـ DOM مباشرة.
- الـ Route بتاع الـ AJAX يفضل في `routes/web.php` (مش `api.php`) لأن المشروع session-based، فبيستخدم middleware `auth` العادي مش `sanctum`.

شوف المثال الكامل التالي.

---

## مثال كامل: CRUD + Search بدون Reload

مثال متكامل وقابل للنسخ المباشر: نظام **Tasks** بسيط فيه إضافة، بحث، تعديل سريع، وحذف — كله بدون ما الصفحة تعمل reload.

### 1. Migration

```bash
php artisan make:model Task -m
```

```php
// database/migrations/xxxx_create_tasks_table.php
public function up(): void
{
    Schema::create('tasks', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->text('notes')->nullable();
        $table->boolean('is_done')->default(false);
        $table->timestamps();
    });
}
```

```bash
php artisan migrate
```

### 2. Model

```php
// app/Models/Task.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['title', 'notes', 'is_done'];

    protected function casts(): array
    {
        return ['is_done' => 'boolean'];
    }
}
```

### 3. Form Request (نفس القواعد لل store وال update — بدون تكرار)

```bash
php artisan make:request TaskRequest
```

```php
// app/Http/Requests/TaskRequest.php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
```

### 4. Controller

كل الـ endpoints بترجع JSON عشان الـ JavaScript يستخدمها، ما عدا `index` اللي بيرجع View عادي أول تحميل.

```php
// app/Http/Controllers/TaskController.php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    // تحميل الصفحة أول مرة (Server-rendered)
    public function index(): View
    {
        $tasks = Task::latest()->paginate(10);

        return view('tasks.index', compact('tasks'));
    }

    // البحث — يُستدعى عبر fetch، بيرجع JSON بس
    public function search(Request $request): JsonResponse
    {
        $tasks = Task::query()
            ->when($request->filled('q'), fn ($query) => $query
                ->where('title', 'like', '%'.$request->string('q').'%'))
            ->latest()
            ->paginate(10);

        return response()->json([
            'html' => view('tasks.partials.rows', compact('tasks'))->render(),
            'pagination' => view('tasks.partials.pagination', compact('tasks'))->render(),
        ]);
    }

    // إضافة — بدون reload
    public function store(TaskRequest $request): JsonResponse
    {
        $task = Task::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء المهمة بنجاح!',
            'row' => view('tasks.partials.row', compact('task'))->render(),
        ]);
    }

    // تعديل سريع (عنوان أو حالة الإنجاز) — بدون reload
    public function update(TaskRequest $request, Task $task): JsonResponse
    {
        $task->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث المهمة!',
        ]);
    }

    // تبديل حالة "منجز" — بدون reload
    public function toggle(Task $task): JsonResponse
    {
        $task->update(['is_done' => ! $task->is_done]);

        return response()->json(['success' => true, 'is_done' => $task->is_done]);
    }

    // حذف — بدون reload
    public function destroy(Task $task): JsonResponse
    {
        $task->delete();

        return response()->json(['success' => true, 'message' => 'تم حذف المهمة.']);
    }
}
```

### 5. Routes (`routes/web.php`)

مهم: كله جوه مجموعة `auth` العادية (session middleware)، مش `api.php`، لأن المشروع مش بيستخدم tokens.

```php
use App\Http\Controllers\TaskController;

Route::middleware('auth')->group(function () {
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/search', [TaskController::class, 'search'])->name('tasks.search');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::patch('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::patch('/tasks/{task}/toggle', [TaskController::class, 'toggle'])->name('tasks.toggle');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
});
```

### 6. Views

**`resources/views/tasks/partials/row.blade.php`** (صف واحد — يُعاد استخدامه في التحميل الأول وبعد كل تحديث):

```blade
<tr id="task-{{ $task->id }}">
    <td>
        <input
            type="checkbox"
            class="form-check-input toggle-task"
            data-id="{{ $task->id }}"
            @checked($task->is_done)
        >
    </td>
    <td>
        <span class="task-title {{ $task->is_done ? 'text-decoration-line-through text-muted' : '' }}">
            {{ $task->title }}
        </span>
    </td>
    <td class="text-muted small">{{ $task->notes }}</td>
    <td>
        <button class="btn btn-sm btn-warning edit-task" data-id="{{ $task->id }}" data-title="{{ $task->title }}">
            Edit
        </button>
        <button class="btn btn-sm btn-danger delete-task" data-id="{{ $task->id }}">
            Delete
        </button>
    </td>
</tr>
```

**`resources/views/tasks/partials/rows.blade.php`** (تُستخدم فقط عند البحث لإعادة رسم كل الصفوف):

```blade
@forelse ($tasks as $task)
    @include('tasks.partials.row', ['task' => $task])
@empty
    <tr>
        <td colspan="4" class="text-center text-muted py-4">لا توجد مهام مطابقة.</td>
    </tr>
@endforelse
```

**`resources/views/tasks/partials/pagination.blade.php`:**

```blade
<div class="d-flex justify-content-center">
    {{ $tasks->links() }}
</div>
```

**`resources/views/tasks/index.blade.php`** (الصفحة الرئيسية):

```blade
@extends('layouts.app')

@section('title', 'Tasks')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Tasks</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTaskModal">
        + Add Task
    </button>
</div>

<div class="mb-3">
    <input
        type="text"
        id="searchInput"
        class="form-control"
        placeholder="ابحث في المهام..."
    >
</div>

<div class="card">
    <div class="card-body">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th style="width: 40px;"></th>
                    <th>Title</th>
                    <th>Notes</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="tasksTableBody">
                @include('tasks.partials.rows')
            </tbody>
        </table>

        <div id="paginationWrapper">
            @include('tasks.partials.pagination')
        </div>
    </div>
</div>

<!-- Modal: Add Task -->
<div class="modal fade" id="createTaskModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="createTaskForm">
                <div class="modal-header">
                    <h5 class="modal-title">Add Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <x-input name="title" label="Title" required />
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <x-button type="submit">Save</x-button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const tableBody = document.getElementById('tasksTableBody');
    const createModalEl = document.getElementById('createTaskModal');
    const createModal = new bootstrap.Modal(createModalEl);

    // ─────────────────────────────────────────
    // 1) البحث بدون Reload (debounced)
    // ─────────────────────────────────────────
    let searchTimeout;
    document.getElementById('searchInput').addEventListener('input', function (e) {
        clearTimeout(searchTimeout);
        const query = e.target.value;

        searchTimeout = setTimeout(() => {
            showLoader();

            fetch(`{{ route('tasks.search') }}?q=${encodeURIComponent(query)}`, {
                headers: { 'Accept': 'application/json' },
            })
                .then(res => res.json())
                .then(data => {
                    hideLoader();
                    tableBody.innerHTML = data.html;
                    document.getElementById('paginationWrapper').innerHTML = data.pagination;
                    bindRowEvents(); // إعادة ربط الأحداث على الصفوف الجديدة
                })
                .catch(() => {
                    hideLoader();
                    toastr.error('حدث خطأ أثناء البحث.');
                });
        }, 400); // ننتظر 400ms بعد آخر حرف قبل ما نبحث
    });

    // ─────────────────────────────────────────
    // 2) إضافة مهمة بدون Reload
    // ─────────────────────────────────────────
    document.getElementById('createTaskForm').addEventListener('submit', function (e) {
        e.preventDefault();
        showLoader();

        const formData = new FormData(this);

        fetch('{{ route('tasks.store') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: formData,
        })
            .then(res => res.json())
            .then(data => {
                hideLoader();

                if (data.success) {
                    tableBody.insertAdjacentHTML('afterbegin', data.row);
                    bindRowEvents();
                    createModal.hide();
                    this.reset();
                    toastr.success(data.message);
                }
            })
            .catch(() => {
                hideLoader();
                toastr.error('حدث خطأ أثناء الحفظ.');
            });
    });

    // ─────────────────────────────────────────
    // 3) أحداث الصفوف (Toggle / Edit / Delete)
    //    لازم تتربط من جديد كل مرة الجدول يتحدث
    // ─────────────────────────────────────────
    function bindRowEvents() {
        // تبديل حالة "منجز"
        document.querySelectorAll('.toggle-task').forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                const taskId = this.dataset.id;
                showLoader();

                fetch(`/tasks/${taskId}/toggle`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                })
                    .then(res => res.json())
                    .then(data => {
                        hideLoader();
                        if (data.success) {
                            const titleSpan = document.querySelector(`#task-${taskId} .task-title`);
                            titleSpan.classList.toggle('text-decoration-line-through', data.is_done);
                            titleSpan.classList.toggle('text-muted', data.is_done);
                        }
                    })
                    .catch(() => {
                        hideLoader();
                        toastr.error('حدث خطأ.');
                        this.checked = !this.checked; // ارجاع الحالة القديمة
                    });
            });
        });

        // تعديل سريع (Prompt بسيط — ممكن تستبدله بـ Modal)
        document.querySelectorAll('.edit-task').forEach(btn => {
            btn.addEventListener('click', function () {
                const taskId = this.dataset.id;
                const currentTitle = this.dataset.title;
                const newTitle = prompt('العنوان الجديد:', currentTitle);

                if (!newTitle || newTitle === currentTitle) return;

                showLoader();

                fetch(`/tasks/${taskId}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ title: newTitle }),
                })
                    .then(res => res.json())
                    .then(data => {
                        hideLoader();
                        if (data.success) {
                            document.querySelector(`#task-${taskId} .task-title`).textContent = newTitle;
                            this.dataset.title = newTitle;
                            toastr.success(data.message);
                        }
                    })
                    .catch(() => {
                        hideLoader();
                        toastr.error('حدث خطأ أثناء التحديث.');
                    });
            });
        });

        // حذف مع تأكيد
        document.querySelectorAll('.delete-task').forEach(btn => {
            btn.addEventListener('click', function () {
                if (!confirm('أنت متأكد من الحذف؟')) return;

                const taskId = this.dataset.id;
                showLoader();

                fetch(`/tasks/${taskId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                })
                    .then(res => res.json())
                    .then(data => {
                        hideLoader();
                        if (data.success) {
                            document.getElementById(`task-${taskId}`).remove();
                            toastr.success(data.message);
                        }
                    })
                    .catch(() => {
                        hideLoader();
                        toastr.error('حدث خطأ أثناء الحذف.');
                    });
            });
        });
    }

    bindRowEvents(); // ربط أولي عند تحميل الصفحة
</script>
@endpush
```

**مهم:** عشان `@push('scripts')` يشتغل، لازم الـ layout يكون فيه `@stack('scripts')` (موجود بالفعل في `layouts/app.blade.php` بعد الـ scripts الأساسية).

### ملخص الـ Pattern:

| العملية | الطريقة | Reload؟ |
|--------|---------|---------|
| تحميل الصفحة أول مرة | `index()` يرجع View عادي | نعم (طبيعي وصحي) |
| البحث | `fetch()` GET + استبدال `innerHTML` | ❌ لا |
| إضافة | `fetch()` POST + إدراج الصف الجديد | ❌ لا |
| تعديل | `fetch()` PATCH + تحديث النص | ❌ لا |
| حذف | `fetch()` DELETE + إزالة الصف | ❌ لا |
| كل عملية | `showLoader()` قبل / `hideLoader()` بعد | — |
| كل نتيجة | `toastr.success()` / `toastr.error()` | — |

---

## رفع الصور مع Preview (MediaService)

المشروع فيه `spatie/laravel-medialibrary` جاهز، مغلّف جوه `App\Services\MediaService` (شوف [MediaService.php](app/Services/MediaService.php)). القاعدة: الـ Controller **مايكلمش Spatie مباشرة أبداً** — يكلم `MediaService` بس.

### 1. الفورم مع Preview قبل الرفع (Vanilla JS)

```blade
<div class="mb-3">
    <label for="image" class="form-label">Product Image</label>
    <input type="file" name="image" id="image" class="form-control" accept="image/*">

    <img id="imagePreview" src="" alt="" class="mt-2 rounded d-none" style="max-width: 200px;">
</div>

@push('scripts')
<script>
    document.getElementById('image').addEventListener('change', function (e) {
        const file = e.target.files[0];
        const preview = document.getElementById('imagePreview');

        if (!file) {
            preview.classList.add('d-none');
            return;
        }

        const reader = new FileReader();
        reader.onload = (event) => {
            preview.src = event.target.result;
            preview.classList.remove('d-none');
        };
        reader.readAsDataURL(file);
    });
</script>
@endpush
```

### 2. الرفع في الـ Controller — عبر MediaService فقط

```php
use App\Services\MediaService;

class ProductController extends Controller
{
    public function __construct(private MediaService $mediaService) {}

    public function store(StoreProductRequest $request)
    {
        $product = Product::create($request->validated());

        if ($request->hasFile('image')) {
            $this->mediaService->upload($product, $request->file('image'), 'images');
        }

        return redirect()->route('products.index')
            ->with('success', 'تم إنشاء المنتج بنجاح!');
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->validated());

        if ($request->hasFile('image')) {
            $oldImage = $product->getFirstMedia('images');
            $this->mediaService->update($product, $request->file('image'), $oldImage, 'images');
        }

        return redirect()->back()->with('success', 'تم التحديث بنجاح!');
    }

    public function destroy(Product $product)
    {
        $this->mediaService->deleteMedia($product, 'images'); // يمسح كل صور الـ collection
        $product->delete();

        return redirect()->route('products.index')->with('success', 'تم الحذف.');
    }
}
```

**مهم:** الـ Model لازم يعمل `implement HasMedia` ويستخدم `InteractsWithMedia`:

```php
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use InteractsWithMedia;
}
```

### 3. عرض الصورة في الـ View

```blade
@if ($product->hasMedia('images'))
    <img src="{{ $product->getFirstMediaUrl('images') }}" alt="{{ $product->name }}" class="img-thumbnail" style="max-width: 150px;">
@else
    <span class="text-muted">لا توجد صورة</span>
@endif
```

### 4. رفع بدون Reload (AJAX)

نفس فكرة قسم CRUD فوق — استخدم `FormData` (مش JSON) عشان الملفات، والـ Controller يرجع JSON فيه رابط الصورة الجديدة:

```js
document.getElementById('createProductForm').addEventListener('submit', function (e) {
    e.preventDefault();
    showLoader();

    const formData = new FormData(this); // بياخد كل الحقول + الملف تلقائياً

    fetch('{{ route('products.store') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        },
        body: formData, // من غير Content-Type header — المتصفح بيحطه لوحده مع الـ boundary
    })
        .then(res => res.json())
        .then(data => {
            hideLoader();
            if (data.success) {
                toastr.success(data.message);
            }
        })
        .catch(() => {
            hideLoader();
            toastr.error('حدث خطأ أثناء الرفع.');
        });
});
```

⚠️ **لا تحط** `'Content-Type': 'multipart/form-data'` يدوي في الـ headers — المتصفح محتاج يحسب الـ `boundary` بنفسه، ولو حطيته يدوي هيبعت الملف تالف.

---

## Pagination بـ Bootstrap

Laravel افتراضياً بيرسم الـ pagination بـ **Tailwind classes** (`pagination::tailwind`)، والمشروع ده بيستخدم Bootstrap بس — يعني `{{ $items->links() }}` كان هيرجع HTML من غير أي تنسيق فعلي.

**متصلح بالفعل** في [AppServiceProvider.php](app/Providers/AppServiceProvider.php):

```php
use Illuminate\Pagination\Paginator;

public function boot(): void
{
    Paginator::useBootstrapFive();
}
```

يعني أي `{{ $tasks->links() }}` أو `{{ $products->links() }}` في أي View هيطلع تلقائياً بتنسيق Bootstrap 5 صحيح، من غير ما تعمل حاجة زيادة.

---

## ملخص سريع لكل حاجة

| الحاجة | الملف | الطريقة |
|------|------|--------|
| **Layout** | View | `@extends('layouts.app')` أو `.admin` أو `.public` أو `.guest` |
| **Toastr (redirect عادي)** | Controller | `->with('success', 'رسالة')` |
| **Toastr (AJAX)** | JavaScript | `toastr.success('رسالة')` |
| **Loader (form عادي)** | تلقائي | يظهر لوحده، أو `data-no-loader` لتعطيله |
| **Loader (AJAX)** | JavaScript | `showLoader()` / `hideLoader()` |
| **بحث بدون Reload** | JavaScript | `fetch()` GET + استبدال `innerHTML` |
| **CRUD بدون Reload** | JavaScript | `fetch()` + JSON response من الـ Controller |
| **رفع صورة** | Controller | `$this->mediaService->upload($model, $file, 'collection')` |
| **رفع صورة بدون Reload** | JavaScript | `FormData` + `fetch()` (من غير `Content-Type` يدوي) |
| **Pagination** | تلقائي | `{{ $items->links() }}` — Bootstrap 5 جاهز |
| **Scripts خاصة بصفحة** | View | `@push('scripts') ... @endpush` |

**Done! 🚀**
