<?php

use Database\Seeders\LandingContentSeeder;

beforeEach(function () {
    $this->seed(LandingContentSeeder::class);
});

test('the population map renders for guests', function () {
    $response = $this->get(route('population-map.public'));

    $response->assertOk();
    $response->assertSee('Peta Populasi');
    $response->assertSee('2025');
});

test('the population map loads leaflet', function () {
    $response = $this->get(route('population-map.public'));

    $response->assertSee('leaflet.js', escape: false);
    $response->assertSee('x-data', escape: false);
});

test('every year dataset is committed under the public maps directory', function () {
    foreach (['2021', '2022', '2023', '2024', '2025'] as $year) {
        expect(public_path("maps/pl_{$year}.geojson"))->toBeFile();
    }
});
