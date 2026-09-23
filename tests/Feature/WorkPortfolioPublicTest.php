<?php

namespace Tests\Feature;

use App\Models\WorkProject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class WorkPortfolioPublicTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    private function makeProject(array $overrides = []): WorkProject
    {
        $project = WorkProject::create(array_merge([
            'title_ar' => 'فيلا الشيخ زايد',
            'title_en' => 'Sheikh Zayed Villa',
            'description_ar' => 'حمام سباحة فاخر بتصميم عصري.',
            'description_en' => 'A luxury pool with a modern design.',
            'location' => 'Sheikh Zayed, Egypt',
        ], $overrides));

        $project->addMedia(UploadedFile::fake()->image('pool.jpg'))->toMediaCollection('gallery');

        return $project;
    }

    public function test_empty_portfolio_shows_empty_state_message(): void
    {
        $response = $this->get(route('work.index'));

        $response->assertOk();
        $response->assertSee(__('site.work.empty_state'));
    }

    public function test_portfolio_grid_shows_thumbnail_and_title(): void
    {
        $project = $this->makeProject();

        $response = $this->get(route('work.index'));

        $response->assertOk();
        $response->assertSee($project->title_ar);
    }

    public function test_project_detail_page_shows_gallery_description_and_location(): void
    {
        $project = $this->makeProject();
        $project->addMedia(UploadedFile::fake()->image('pool-2.jpg'))->toMediaCollection('gallery');

        $response = $this->get(route('work.show', $project));

        $response->assertOk();
        $response->assertSee($project->description_ar);
        $response->assertSee($project->location);
        $this->assertCount(2, $project->fresh()->galleryImages());
    }

    public function test_missing_project_shows_not_found_view_with_link_back(): void
    {
        $response = $this->get(route('work.show', 999));

        $response->assertNotFound();
        $response->assertSee(__('site.work.not_found_title'));
        $response->assertSee(route('work.index'), false);
    }
}
