<?php

namespace App\Livewire\Cms\Settings;

use App\Models\Setting;
use App\Models\User;
use Flux\Flux;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts::cms')]
#[Title('Settings')]
class Index extends Component
{
    #[Url(except: 'general')]
    public string $tab = 'general';

    /** @var array<string, string> */
    public array $general = [];

    /** @var array<string, string> */
    public array $seo = [];

    /** @var array<string, string> */
    public array $social = [];

    public function mount(): void
    {
        $general = Setting::group('general');
        $seo = Setting::group('seo');
        $social = Setting::group('social');

        $this->general = [
            'site_name' => (string) ($general['site_name'] ?? config('app.name')),
            'tagline' => (string) ($general['tagline'] ?? ''),
            'meta_description' => (string) ($general['meta_description'] ?? ''),
            'login_cta_label' => (string) ($general['login_cta_label'] ?? ''),
        ];

        $this->seo = [
            'meta_title_template' => (string) ($seo['meta_title_template'] ?? ':title - :site'),
        ];

        $this->social = [
            'instagram_url' => (string) ($social['instagram_url'] ?? ''),
            'twitter_url' => (string) ($social['twitter_url'] ?? ''),
            'youtube_url' => (string) ($social['youtube_url'] ?? ''),
        ];
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    #[Computed]
    public function tabs(): array
    {
        return [
            ['value' => 'general', 'label' => 'General'],
            ['value' => 'seo', 'label' => 'SEO'],
            ['value' => 'social', 'label' => 'Social'],
            ['value' => 'team', 'label' => 'Team'],
        ];
    }

    /**
     * @return Collection<int, User>
     */
    #[Computed]
    public function team(): Collection
    {
        return User::query()->orderBy('name')->get();
    }

    public function save(): void
    {
        $validated = $this->validate([
            'general.site_name' => ['required', 'string', 'max:120'],
            'general.tagline' => ['nullable', 'string', 'max:200'],
            'general.meta_description' => ['nullable', 'string', 'max:300'],
            'general.login_cta_label' => ['nullable', 'string', 'max:60'],
            'seo.meta_title_template' => ['nullable', 'string', 'max:120'],
            'social.instagram_url' => ['nullable', 'string', 'max:255'],
            'social.twitter_url' => ['nullable', 'string', 'max:255'],
            'social.youtube_url' => ['nullable', 'string', 'max:255'],
        ]);

        foreach ($validated as $group => $values) {
            foreach ($values as $key => $value) {
                Setting::write($key, $value, $group);
            }
        }

        Flux::toast(variant: 'success', text: __('Settings saved.'));
    }
}
