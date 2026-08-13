<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Map Center
    |--------------------------------------------------------------------------
    | Default center and zoom for the Leaflet map (public + CMS).
    */

    'center' => [
        'lat' => -2.5489,
        'lng' => 118.0149,
        'zoom' => 5,
    ],

    /*
    |--------------------------------------------------------------------------
    | Platforms
    |--------------------------------------------------------------------------
    | Marker source for the public and CMS maps. Replace with a database
    | table when platforms need to be managed from the CMS.
    |
    * @var array<int, array{id: int, name: string, lat: float, lng: float, description: string, status: string, area: string}>
    */

    'items' => [
        ['id' => 1, 'name' => 'Platform Jakarta', 'area' => 'Jakarta, ID', 'lat' => -6.2088, 'lng' => 106.8456, 'description' => 'Pusat operasi utama untuk wilayah barat.', 'status' => 'active'],
        ['id' => 2, 'name' => 'Platform Surabaya', 'area' => 'Surabaya, ID', 'lat' => -7.2575, 'lng' => 112.7521, 'description' => 'Simpul sekunder untuk wilayah timur.', 'status' => 'active'],
        ['id' => 3, 'name' => 'Platform Makassar', 'area' => 'Makassar, ID', 'lat' => -5.1477, 'lng' => 119.4327, 'description' => 'Gerbang pusat Indonesia timur.', 'status' => 'maintenance'],
        ['id' => 4, 'name' => 'Platform Medan', 'area' => 'Medan, ID', 'lat' => 3.5952, 'lng' => 98.6722, 'description' => 'Stasiun relai wilayah utara.', 'status' => 'active'],
        ['id' => 5, 'name' => 'Platform Denpasar', 'area' => 'Denpasar, ID', 'lat' => -8.6705, 'lng' => 115.2126, 'description' => 'Simpul pendukung koridor pariwisata.', 'status' => 'offline'],
    ],

];
