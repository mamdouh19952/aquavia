# Laravel Blade Project — Bootstrap Template

A clean, simple Laravel Blade project with Bootstrap 5, Spatie Media Library, and best practices.

## Quick Setup

```bash
# 1. Install dependencies
composer install
npm install

# 2. Setup environment
cp .env.example .env
php artisan key:generate

# 3. Setup database (SQLite by default in .env)
php artisan migrate

# 4. Start development server
npm run dev    # Vite
php artisan serve
```

## Stack

- **Laravel 12** with Blade templating
- **Bootstrap 5** for styling
- **Spatie Media Library** for file uploads
- **Toastr** for notifications
- **Vite** for asset bundling
- **Axios** for HTTP requests
- **Vanilla JavaScript** for interactivity

## Project Structure

```
app/
├── Services/
│   └── MediaService.php         ← All media uploads go through this
├── Http/
│   └── Controllers/
│       └── [Your controllers here]
└── Models/
    └── [Your models here]

resources/
├── views/
│   ├── layouts/
│   │   ├── app.blade.php        (authenticated pages)
│   │   └── guest.blade.php      (login, register)
│   ├── components/              (reusable Blade components)
│   ├── partials/
│   │   ├── toast.blade.php
│   │   └── scripts.blade.php
│   ├── admin/                   (admin features)
│   ├── user/                    (user features)
│   ├── dashboard.blade.php
│   └── welcome.blade.php
├── css/
│   └── app.css
└── js/
    ├── app.js
    └── bootstrap.js

routes/
└── web.php                      (all web routes — no API by default)
```

## Key Files

- **CLAUDE.md** — Development rules, patterns, and conventions
- **app/Services/MediaService.php** — Centralized media upload handler
- **resources/views/components/** — Reusable Blade components (button, input, alert, etc.)
- **resources/views/partials/toast.blade.php** — Toastr notification system

## Development Workflow

### 1. Creating a Feature

Follow the checklist in CLAUDE.md:

1. Inspect existing routes (`routes/web.php`)
2. Create a Model (if needed)
3. Create a Form Request (for validation)
4. Create a Controller
5. Add routes
6. Create Blade views
7. Add reusable components as needed

### 2. Uploading Files

Always use `MediaService`:

```php
// In your controller
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

        return redirect()->route('products.show', $product)->with('success', 'Product created!');
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->validated());

        if ($request->hasFile('image')) {
            $oldImage = $product->getFirstMedia('images');
            $this->mediaService->update($product, $request->file('image'), $oldImage, 'images');
        }

        return redirect()->back()->with('success', 'Product updated!');
    }
}
```

### 3. Using Blade Components

```blade
<!-- inputs -->
<x-input name="title" label="Product Title" placeholder="Enter title..." required />

<!-- buttons -->
<x-button type="submit" variant="primary" size="lg">Save Product</x-button>

<!-- alerts -->
<x-alert type="success">
    Product saved successfully!
</x-alert>
```

### 4. Forms & Validation

```blade
<form method="POST" action="{{ route('products.store') }}">
    @csrf

    <x-input name="title" label="Title" required />
    <x-input name="price" type="number" label="Price" step="0.01" required />

    <x-button type="submit">Create</x-button>
</form>
```

### 5. Notifications

```php
// In controller
return redirect()->route('products.show', $product)
    ->with('success', 'Product created successfully!');

// Blade will auto-render it via partials/toast.blade.php
```

## Common Tasks

### Adding a New Page

1. Create route in `routes/web.php`
2. Create view in `resources/views/`
3. Use `@extends('layouts.app')` for authenticated pages
4. Use `@extends('layouts.guest')` for public/guest pages

### Adding a Modal

```blade
<!-- In your view -->
<div class="modal fade" id="confirmDelete" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Trigger -->
<button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmDelete">
    Delete
</button>
```

### Adding Pagination

```php
// In controller
$items = Item::paginate(15);

// In view
@foreach ($items as $item)
    <!-- render item -->
@endforeach

{{ $items->links() }}  <!-- Bootstrap pagination -->
```

## Do NOT

❌ Do NOT use Vue, React, or other frontend frameworks  
❌ Do NOT create API routes unless explicitly needed  
❌ Do NOT use complex architectures (Repository, Service, Event/Listener) unless necessary  
❌ Do NOT call Spatie Media Library directly — use MediaService  
❌ Do NOT put business logic in Blade templates  
❌ Do NOT trust client input — always validate server-side  

## Do

✅ Keep controllers focused and simple  
✅ Use Form Requests for validation  
✅ Create reusable Blade components  
✅ Use Bootstrap utilities for styling  
✅ Eager-load relationships (prevent N+1)  
✅ Use Toastr for notifications  
✅ Write semantic HTML  
✅ Test important flows  

## Resources

- [Laravel 12 Docs](https://laravel.com/docs)
- [Bootstrap 5 Docs](https://getbootstrap.com/docs)
- [Spatie Media Library](https://spatie.be/docs/laravel-medialibrary/v11/introduction)
- [Laravel Best Practices](https://github.com/alexeymezenin/laravel-best-practices)

---

**Start building!** Follow the patterns in CLAUDE.md and enjoy clean, maintainable code.
