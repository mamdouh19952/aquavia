<?php

namespace Tests\Feature;

use Tests\TestCase;

class BilingualStaticSiteTest extends TestCase
{
    public function test_home_page_defaults_to_arabic_and_rtl(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('dir="rtl"', false);
        $response->assertSee('نصمم', false);
    }

    public function test_all_static_pages_render_successfully(): void
    {
        $this->get('/')->assertOk();
        $this->get('/about')->assertOk();
        $this->get('/services')->assertOk();
        $this->get('/contact')->assertOk();
    }

    public function test_switching_language_updates_direction_and_persists(): void
    {
        $response = $this->get(route('locale.switch', 'en'));
        $response->assertRedirect();

        $home = $this->get('/');
        $home->assertOk();
        $home->assertSee('dir="ltr"', false);
        $home->assertSee('We Design', false);

        $about = $this->get('/about');
        $about->assertOk();
        $about->assertSee('dir="ltr"', false);
    }

    public function test_switching_to_an_unsupported_locale_is_rejected(): void
    {
        $this->get('/lang/fr')->assertNotFound();
    }
}
