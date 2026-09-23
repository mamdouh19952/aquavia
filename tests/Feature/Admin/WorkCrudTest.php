<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\WorkProject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class WorkCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('admin.work.index'))->assertRedirect('/login');
        $this->get(route('admin.work.create'))->assertRedirect('/login');
    }

    public function test_non_admin_users_cannot_access_work_admin(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)->get(route('admin.work.index'))->assertForbidden();
    }

    public function test_admin_can_create_a_project_with_photos(): void
    {
        $response = $this->actingAs($this->admin())->post(route('admin.work.store'), [
            'title_ar' => 'فيلا الشيخ زايد',
            'title_en' => 'Sheikh Zayed Villa',
            'description_ar' => 'وصف بالعربي',
            'description_en' => 'English description',
            'location' => 'Sheikh Zayed, Egypt',
            'photos' => [
                UploadedFile::fake()->image('pool1.jpg'),
                UploadedFile::fake()->image('pool2.jpg'),
            ],
        ]);

        $response->assertRedirect(route('admin.work.index'));
        $response->assertSessionHas('success');

        $project = WorkProject::firstOrFail();
        $this->assertSame('Sheikh Zayed Villa', $project->title_en);
        $this->assertCount(2, $project->galleryImages());

        // Reflected immediately on the public site (FR-011)
        $this->get(route('work.index'))->assertSee('فيلا الشيخ زايد');
    }

    public function test_creating_a_project_without_required_fields_fails_validation_and_keeps_other_input(): void
    {
        $response = $this->actingAs($this->admin())->post(route('admin.work.store'), [
            'title_ar' => 'فيلا الشيخ زايد',
            'title_en' => '', // missing
            'description_ar' => 'وصف بالعربي',
            'description_en' => 'English description',
            'location' => 'Sheikh Zayed, Egypt',
            'photos' => [],
        ]);

        $response->assertSessionHasErrors(['title_en', 'photos']);
        $response->assertSessionHasInput('title_ar', 'فيلا الشيخ زايد');
        $this->assertSame(0, WorkProject::count());
    }

    public function test_admin_can_update_a_project(): void
    {
        $project = WorkProject::create([
            'title_ar' => 'قديم',
            'title_en' => 'Old Title',
            'description_ar' => 'قديم',
            'description_en' => 'Old description',
            'location' => 'Old Location',
        ]);
        $project->addMedia(UploadedFile::fake()->image('old.jpg'))->toMediaCollection('gallery');

        $response = $this->actingAs($this->admin())->put(route('admin.work.update', $project), [
            'title_ar' => 'جديد',
            'title_en' => 'New Title',
            'description_ar' => 'جديد',
            'description_en' => 'New description',
            'location' => 'New Location',
        ]);

        $response->assertRedirect(route('admin.work.index'));
        $this->assertSame('New Location', $project->fresh()->location);

        $this->get(route('work.show', $project))->assertSee('New Location');
    }

    public function test_admin_can_delete_a_project_and_it_disappears_publicly(): void
    {
        $project = WorkProject::create([
            'title_ar' => 'للحذف',
            'title_en' => 'To Delete',
            'description_ar' => 'وصف',
            'description_en' => 'Description',
            'location' => 'Somewhere',
        ]);

        $response = $this->actingAs($this->admin())->delete(route('admin.work.destroy', $project));

        $response->assertRedirect(route('admin.work.index'));
        $this->assertSame(0, WorkProject::count());
        $this->get(route('work.show', $project->id))->assertNotFound();
    }
}
