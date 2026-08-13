<?php

use Database\Seeders\LandingContentSeeder;

// The page is CMS-driven now, so its copy comes from the seeded content.
beforeEach(function () {
    $this->seed(LandingContentSeeder::class);
});

test('the landing page renders for guests', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('Pertanian');
    $response->assertSee('Lainnya');
});

test('the platform call to action points at the login route', function () {
    $response = $this->get(route('home'));

    $response->assertSee('Masuk Platform');
    $response->assertSee(route('login'), escape: false);
});

test('the landing page renders every section', function () {
    $response = $this->get(route('home'));

    foreach (['beranda', 'tentang', 'data', 'peta', 'fitur', 'kontak'] as $section) {
        $response->assertSee('id="'.$section.'"', escape: false);
    }
});

test('the landing page shows the headline figures from the design', function () {
    $response = $this->get(route('home'));

    $response->assertSeeInOrder([
        '12.4 Juta Ha',
        '+14.2%',
        '38 Provinsi',
        '12.420.000',
        '234.540 Ha',
    ]);
});

test('the landing page serves committed local images rather than remote ones', function () {
    $response = $this->get(route('home'));

    $response->assertSee('/images/landing/hero.jpg', escape: false);
    $response->assertDontSee('images.unsplash.com');

    foreach (['hero', 'feature-1', 'feature-2', 'feature-3', 'feature-4', 'map-bg'] as $image) {
        expect(public_path("images/landing/{$image}.jpg"))->toBeFile();
    }
});

test('the landing page loads alpine so the interactive controls work', function () {
    $response = $this->get(route('home'));

    $response->assertSee('x-data', escape: false);
    $response->assertSee('livewire.js', escape: false);
});

test('every lucide icon resolves to real inline svg', function () {
    $response = $this->get(route('home'));
    $html = $response->getContent();

    // No Flux component tag should survive compilation unrendered.
    expect($html)->not->toContain('<flux:');

    // Navbar, hero, cards, stats, dashboard sidebar and footer all pull icons.
    expect(substr_count($html, 'data-flux-icon'))->toBeGreaterThan(30);
});

test('the indonesia dot map is rendered server side', function () {
    $response = $this->get(route('home'));

    // The ported ellipse-fill algorithm should emit the dot grid plus 3 gold highlights.
    expect(substr_count($response->getContent(), 'fill="#4a8c3f"'))->toBeGreaterThan(400);
    expect(substr_count($response->getContent(), 'fill="#c8a84b"'))->toBe(3);
});
