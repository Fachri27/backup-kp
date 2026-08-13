<?php

namespace Database\Seeders;

use App\Enums\SectionStatus;
use App\Models\PageSection;
use App\Models\Setting;
use Illuminate\Database\Seeder;

/**
 * Seeds the landing page's editable content.
 *
 * These values mirror what the Blade partials used to hardcode, so running this
 * seeder leaves the public page rendering exactly as it did before the CMS existed.
 */
class LandingContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedSettings();

        $this->seedSection('navigation', 'Navigation', 'Main menu links in the header and mobile menu', 0, [], 'link', [
            ['label' => 'Beranda', 'href' => '#beranda'],
            ['label' => 'Tentang', 'href' => '#tentang'],
            ['label' => 'Data', 'href' => '#data'],
            ['label' => 'Peta', 'href' => '#peta'],
            ['label' => 'Fitur', 'href' => '#fitur'],
            ['label' => 'Kontak', 'href' => '#kontak'],
        ]);

        $this->seedSection('hero', 'Hero', 'The opening banner, headline and floating stat cards', 1, [
            'eyebrow' => ['Data', 'Wilayah', 'Perubahan'],
            'title_line_1' => 'Pertanian',
            'title_line_2' => 'Lainnya',
            'description' => 'Eksplorasi data pertanian lainnya di Indonesia melalui peta interaktif dan visualisasi modern.',
            'primary_cta_label' => 'Jelajahi Platform',
            'primary_cta_href' => '#peta',
            'secondary_cta_label' => 'Lihat Demo',
            'secondary_cta_href' => '#fitur',
            'scroll_label' => 'SCROLL KE BAWAH',
            'image' => 'images/landing/hero.jpg',
            'image_alt' => 'Terasering persawahan dari udara',
            'trend_bars' => [3, 4, 5, 6, 7, 6, 8, 9, 10, 11, 10, 12],
        ], 'stat_card', [
            [
                'icon' => 'leaf',
                'tint' => 'bg-tani-mint',
                'icon_color' => 'text-tani-green',
                'value' => '12.4 Juta Ha',
                'bars' => false,
                'lines' => [
                    ['text' => 'Total Area', 'style' => 'muted'],
                    ['text' => 'Pertanian Lainnya', 'style' => 'accent'],
                ],
            ],
            [
                'icon' => 'trending-up',
                'tint' => 'bg-tani-sand',
                'icon_color' => 'text-tani-gold',
                'value' => '+14.2%',
                'bars' => true,
                'lines' => [
                    ['text' => 'Perubahan 2021–2024', 'style' => 'muted'],
                ],
            ],
            [
                'icon' => 'map-pin',
                'tint' => 'bg-tani-mint',
                'icon_color' => 'text-tani-green',
                'value' => '38 Provinsi',
                'bars' => false,
                'lines' => [
                    ['text' => '514 Kab/Kota', 'style' => 'accent'],
                    ['text' => 'Tersebar di Indonesia', 'style' => 'muted'],
                ],
            ],
        ]);

        $this->seedSection('about', 'About', 'The "Mengenal Pertanian Lainnya" intro and its four cards', 2, [
            'eyebrow' => 'Mengenal Pertanian Lainnya',
            'heading_line_1' => 'Lebih dari',
            'heading_line_2' => 'sekadar lahan.',
            'body' => 'Pertanian Lainnya mencakup lahan pertanian yang tidak termasuk dalam kategori sawah dan perkebunan, seperti ladang, tegalan, kebun campuran, dan lahan hortikultura lainnya.',
            'cta_label' => 'Selengkapnya',
            'cta_href' => '#data',
        ], 'feature', [
            [
                'icon' => 'leaf',
                'title' => 'Beragam Lahan',
                'description' => 'Mencakup berbagai jenis lahan pertanian lainnya.',
                'number' => '01',
                'image' => 'images/landing/feature-1.jpg',
                'accent' => 'bg-tani-moss',
            ],
            [
                'icon' => 'satellite',
                'title' => 'Data Spasial',
                'description' => 'Bersumber dari citra satelit dan teknologi modern.',
                'number' => '02',
                'image' => 'images/landing/feature-2.jpg',
                'accent' => 'bg-tani-bark',
            ],
            [
                'icon' => 'map-pin',
                'title' => 'Sebaran Wilayah',
                'description' => 'Tersebar luas di seluruh wilayah Indonesia.',
                'number' => '03',
                'image' => 'images/landing/feature-3.jpg',
                'accent' => 'bg-tani-pine',
            ],
            [
                'icon' => 'trending-up',
                'title' => 'Perubahan Dinamis',
                'description' => 'Dipantau dari waktu ke waktu secara berkala.',
                'number' => '04',
                'image' => 'images/landing/feature-4.jpg',
                'accent' => 'bg-tani-moss',
            ],
        ]);

        $this->seedSection('stats', 'Stats banner', 'The dark "Data Dalam Angka" figures strip', 3, [
            'label_line_1' => 'Data Dalam',
            'label_line_2' => 'Angka',
        ], 'stat', [
            ['icon' => 'map', 'value' => '38', 'label' => 'Provinsi', 'sub' => 'Tersebar di Indonesia'],
            ['icon' => 'building-2', 'value' => '514', 'label' => 'Kab/Kota', 'sub' => 'Seluruh Indonesia'],
            ['icon' => 'leaf', 'value' => '12.420.000', 'label' => 'Hektar', 'sub' => 'Total Area Pertanian Lainnya'],
            ['icon' => 'calendar', 'value' => '2021–2024', 'label' => 'Periode Data', 'sub' => '4 Tahun Terakhir'],
        ]);

        $this->seedSection('map', 'Map', 'The Indonesia dot map, its callouts and surrounding copy', 4, [
            'eyebrow' => 'Siap Menjelajahi?',
            'heading_1' => 'Temukan data.',
            'heading_2' => 'Pahami perubahan.',
            'body' => 'Gunakan peta interaktif kami untuk melihat sebaran, luas, dan perubahan pertanian lainnya di seluruh Indonesia.',
            'cta_label' => 'Buka Peta Interaktif',
            'cta_href' => '#fitur',
            'circle_lines' => ['Jelajahi', 'Seluruh', 'Indonesia'],
            'circle_href' => '#fitur',
        ], 'callout', [
            ['name' => 'Aceh', 'area' => '321.450 Ha', 'change' => '+8.7%', 'position' => 'left-0 top-2'],
            ['name' => 'Kalimantan Timur', 'area' => '612.530 Ha', 'change' => '+12.1%', 'position' => 'right-24 top-0'],
            ['name' => 'NTB', 'area' => '410.120 Ha', 'change' => '+9.3%', 'position' => 'bottom-6 left-[46%]'],
        ]);

        $this->seedSection('dashboard', 'Dashboard preview', 'The product mockup: header copy, detail card and footer figures', 5, [
            'title' => 'Peta Interaktif',
            'subtitle' => 'Eksplorasi sebaran dan perubahan pertanian lainnya di Indonesia',
            'search_placeholder' => 'Cari provinsi atau kabupaten...',
            'help_title' => 'Butuh Bantuan?',
            'help_link' => 'Pusat Bantuan',
            'map_image' => 'images/landing/map-bg.jpg',
            'detail' => [
                'name' => 'Bengkulu',
                'category' => 'Pertanian Lainnya',
                'value' => '234.540 Ha',
                'change_label' => 'Perubahan 2021–2024',
                'change' => '+12.4%',
                'cta_label' => 'Lihat Detail',
                'bars' => [4, 5, 6, 7, 8, 9, 10, 11, 12],
            ],
        ], 'bottom_stat', [
            ['label' => 'Total Area (2024)', 'value' => '234.540 Ha', 'sub' => 'Pertanian Lainnya'],
            ['label' => 'Perubahan 2021–2024', 'value' => '+12.4%', 'sub' => 'Dibanding 2021'],
            ['label' => 'Trend Perubahan', 'value' => '↑ Naik', 'sub' => '2021–2024'],
            ['label' => 'Kab/Kota Terluas', 'value' => 'Kepahiang', 'sub' => '45.230 Ha'],
            ['label' => 'Provinsi Terluas', 'value' => 'Kalimantan Timur', 'sub' => '1.345.780 Ha'],
        ]);

        $this->seedSection('footer', 'Footer', 'Closing blurb, social links and copyright', 6, [
            'blurb' => 'Platform data pertanian lainnya Indonesia dengan visualisasi modern dan peta interaktif.',
            'copyright' => 'Pertanian Lainnya. All rights reserved.',
        ], 'link', []);
    }

    private function seedSettings(): void
    {
        $general = [
            'site_name' => 'Pertanian Lainnya',
            'tagline' => 'Platform Data Pertanian Indonesia',
            'meta_description' => 'Eksplorasi data pertanian lainnya di Indonesia melalui peta interaktif dan visualisasi modern.',
            'login_cta_label' => 'Masuk Platform',
        ];

        foreach ($general as $key => $value) {
            Setting::write($key, $value, 'general');
        }

        $social = [
            'instagram_url' => '#kontak',
            'twitter_url' => '#kontak',
            'youtube_url' => '#kontak',
        ];

        foreach ($social as $key => $value) {
            Setting::write($key, $value, 'social');
        }

        Setting::write('meta_title_template', ':title - :site', 'seo');
    }

    /**
     * @param  array<string, mixed>  $content
     * @param  array<int, array<string, mixed>>  $items
     */
    private function seedSection(
        string $key,
        string $name,
        string $description,
        int $sortOrder,
        array $content,
        string $itemType,
        array $items,
    ): void {
        $section = PageSection::query()->updateOrCreate(
            ['key' => $key],
            [
                'name' => $name,
                'description' => $description,
                'content' => $content,
                'status' => SectionStatus::Published,
                'sort_order' => $sortOrder,
            ],
        );

        $section->items()->delete();

        foreach ($items as $index => $data) {
            $section->items()->create([
                'type' => $itemType,
                'sort_order' => $index,
                'is_published' => true,
                'data' => $data,
            ]);
        }
    }
}
