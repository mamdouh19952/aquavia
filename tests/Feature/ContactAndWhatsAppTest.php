<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactAndWhatsAppTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'company.social.facebook' => 'https://facebook.com/aquaviapools',
            'company.social.instagram' => 'https://instagram.com/aquaviapools',
        ]);
    }

    public function test_contact_page_shows_phone_email_address_and_social_links_in_arabic(): void
    {
        $response = $this->get(route('contact'));

        $response->assertOk();
        $response->assertSee(config('company.phone'));
        $response->assertSee(config('company.email'));
        $response->assertSee(config('company.address'));
        $response->assertSee('https://facebook.com/aquaviapools', false);
        $response->assertSee('https://instagram.com/aquaviapools', false);
    }

    public function test_contact_page_shows_the_same_details_in_english(): void
    {
        $this->get(route('locale.switch', 'en'));

        $response = $this->get(route('contact'));

        $response->assertOk();
        $response->assertSee(config('company.phone'));
        $response->assertSee(config('company.email'));
    }

    public function test_whatsapp_button_is_present_on_public_pages(): void
    {
        $expectedLink = 'https://wa.me/'.config('company.whatsapp_number');

        $this->get('/')->assertSee($expectedLink, false);
        $this->get(route('work.index'))->assertSee($expectedLink, false);
    }
}
