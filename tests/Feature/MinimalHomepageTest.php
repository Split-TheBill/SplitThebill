<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MinimalHomepageTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_keeps_the_catalog_order_status_and_help_resources(): void
    {
        $this->get(route('front.index'))
            ->assertOk()
            ->assertSee('id="Products"', false)
            ->assertSee('id="How-It-Works"', false)
            ->assertSee('id="FAQ"', false)
            ->assertSee('id="Resources"', false)
            ->assertSee('href="'.route('front.check_booking').'"', false)
            ->assertSee('Panduan pemesanan')
            ->assertSee('Cek status pesanan')
            ->assertSee('Layanan sedang kami siapkan');

        $this->get(route('front.check_booking'))->assertOk();
    }

    public function test_homepage_omits_unverified_marketing_sections(): void
    {
        $this->get(route('front.index'))
            ->assertOk()
            ->assertDontSee('Statistik Split TheBill')
            ->assertDontSee('16.500+')
            ->assertDontSee('2.209+')
            ->assertDontSee('id="Payment-Method"', false)
            ->assertDontSee('supported-payments.png')
            ->assertDontSee('id="Happy-Customer"', false)
            ->assertDontSee('Testimoni');
    }

    public function test_product_remains_orderable_without_fabricated_social_proof(): void
    {
        $product = Product::query()->create([
            'name' => 'Netflix Premium',
            'thumbnail' => 'products/netflix-thumbnail.png',
            'photo' => 'products/netflix-logo.png',
            'about' => 'Shared premium subscription.',
            'tagline' => 'Watch together.',
            'price' => 100_000,
            'price_per_person' => 50_000,
            'duration' => '1 Bulan',
            'capacity' => 2,
            'is_popular' => true,
        ]);

        $this->get(route('front.index'))
            ->assertOk()
            ->assertSee('Netflix Premium')
            ->assertSee('href="'.route('front.details', $product).'"', false);

        $this->get(route('front.details', $product))
            ->assertOk()
            ->assertSee('Shared premium subscription.')
            ->assertSee('href="'.route('front.booking', $product).'"', false)
            ->assertDontSee('2.120 ulasan')
            ->assertDontSee('5.219+');
    }
}
