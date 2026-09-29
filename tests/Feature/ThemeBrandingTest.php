<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThemeBrandingTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_has_distinct_logos_for_light_and_dark_themes(): void
    {
        $this->get(route('front.index'))
            ->assertOk()
            ->assertSee('brand-logo--light', false)
            ->assertSee('brand-logo--dark', false)
            ->assertSee('assets/images/logos/logoo.svg', false)
            ->assertSee('assets/images/logos/logos.svg', false);
    }
}
