<?php

use App\Enums\SectionStatus;
use App\Livewire\Cms\Content;
use App\Livewire\Cms\Media;
use App\Livewire\Cms\Settings as CmsSettings;
use App\Models\Media as MediaModel;
use App\Models\PageSection;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\LandingContentSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(LandingContentSeeder::class);
});

test('guests cannot reach the cms', function (string $route) {
    $this->get($route)->assertRedirect(route('login'));
})->with([
    fn () => route('cms.dashboard'),
    fn () => route('cms.content.index'),
    fn () => route('cms.media.index'),
    fn () => route('cms.settings.index'),
]);

test('authenticated users can open every cms screen', function (string $route) {
    $this->actingAs(User::factory()->create());

    $this->get($route)->assertOk();
})->with([
    fn () => route('cms.dashboard'),
    fn () => route('cms.content.index'),
    fn () => route('cms.media.index'),
    fn () => route('cms.settings.index'),
    fn () => route('cms.content.edit', PageSection::query()->where('key', 'hero')->first()),
]);

test('the content list filters by search and status', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(Content\Index::class)
        ->assertSee('Hero')
        ->assertSee('Footer')
        ->set('search', 'Hero')
        ->assertSee('Hero')
        ->assertDontSee('Footer')
        ->set('search', '')
        ->set('filter', SectionStatus::Draft->value)
        ->assertDontSee('Hero');
});

test('bulk actions change section visibility', function () {
    $this->actingAs(User::factory()->create());

    $hero = PageSection::query()->where('key', 'hero')->firstOrFail();

    Livewire::test(Content\Index::class)
        ->set('selected', [$hero->id])
        ->call('applyStatus', SectionStatus::Draft->value);

    expect($hero->fresh()->status)->toBe(SectionStatus::Draft);
});

test('editing a section persists copy and shows up on the landing page', function () {
    $this->actingAs(User::factory()->create());

    $hero = PageSection::query()->where('key', 'hero')->firstOrFail();

    $component = Livewire::test(Content\Edit::class, ['section' => $hero]);

    // The editor derives its fields from the stored JSON, so find the description by path.
    $fields = $component->get('fields');
    $index = collect($fields)->search(fn (array $field): bool => $field['path'] === 'description');

    $component->set("fields.{$index}.value", 'Data pertanian yang diperbarui.')
        ->call('save')
        ->assertHasNoErrors();

    expect($hero->fresh()->copy('description'))->toBe('Data pertanian yang diperbarui.');

    $this->get(route('home'))->assertSee('Data pertanian yang diperbarui.');
});

test('the editor round-trips list and nested values without corrupting them', function () {
    $this->actingAs(User::factory()->create());

    $hero = PageSection::query()->where('key', 'hero')->firstOrFail();

    Livewire::test(Content\Edit::class, ['section' => $hero])
        ->call('save')
        ->assertHasNoErrors();

    $fresh = $hero->fresh();

    expect($fresh->copy('eyebrow'))->toBe(['Data', 'Wilayah', 'Perubahan'])
        ->and($fresh->copy('trend_bars'))->toBe([3, 4, 5, 6, 7, 6, 8, 9, 10, 11, 10, 12]);

    // The dashboard section nests a `detail` object with its own bar list.
    $dashboard = PageSection::query()->where('key', 'dashboard')->firstOrFail();

    Livewire::test(Content\Edit::class, ['section' => $dashboard])
        ->call('save')
        ->assertHasNoErrors();

    expect($dashboard->fresh()->copy('detail.value'))->toBe('234.540 Ha')
        ->and($dashboard->fresh()->copy('detail.bars'))->toBe([4, 5, 6, 7, 8, 9, 10, 11, 12]);
});

test('removing a row drops it from the page', function () {
    $this->actingAs(User::factory()->create());

    $about = PageSection::query()->where('key', 'about')->firstOrFail();

    expect($about->items()->count())->toBe(4);

    Livewire::test(Content\Edit::class, ['section' => $about])
        ->call('removeItem', 0);

    expect($about->fresh()->items()->count())->toBe(3);

    $this->get(route('home'))->assertDontSee('Beragam Lahan');
});

test('unpublishing a section hides it from the landing page', function () {
    $this->actingAs(User::factory()->create());

    PageSection::query()->where('key', 'stats')->update(['status' => SectionStatus::Draft->value]);

    $this->get(route('home'))->assertDontSee('Data Dalam');
});

test('settings save and reach the public page', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(CmsSettings\Index::class)
        ->set('general.site_name', 'Data Tani')
        ->set('general.login_cta_label', 'Masuk Sekarang')
        ->call('save')
        ->assertHasNoErrors();

    expect(Setting::read('site_name'))->toBe('Data Tani');

    $this->get(route('home'))->assertSee('Masuk Sekarang');
});

test('settings reject an over-long site name', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(CmsSettings\Index::class)
        ->set('general.site_name', str_repeat('a', 200))
        ->call('save')
        ->assertHasErrors(['general.site_name']);
});

test('uploading an image stores the file and records it', function () {
    Storage::fake('public');

    $this->actingAs(User::factory()->create());

    Livewire::test(Media\Index::class)
        ->set('uploads', [UploadedFile::fake()->image('sawah.jpg', 800, 600)])
        ->assertHasNoErrors();

    $media = MediaModel::query()->firstOrFail();

    expect($media->name)->toBe('sawah.jpg')
        ->and($media->width)->toBe(800)
        ->and($media->height)->toBe(600);

    Storage::disk('public')->assertExists($media->path);
});

test('non image uploads are rejected', function () {
    Storage::fake('public');

    $this->actingAs(User::factory()->create());

    Livewire::test(Media\Index::class)
        ->set('uploads', [UploadedFile::fake()->create('notes.pdf', 100, 'application/pdf')])
        ->assertHasErrors(['uploads.0']);

    expect(MediaModel::query()->count())->toBe(0);
});

test('deleting media removes the row and the file', function () {
    Storage::fake('public');

    $this->actingAs(User::factory()->create());

    Livewire::test(Media\Index::class)
        ->set('uploads', [UploadedFile::fake()->image('hapus.jpg')]);

    $media = MediaModel::query()->firstOrFail();

    Livewire::test(Media\Index::class)->call('delete', $media->id);

    expect(MediaModel::query()->count())->toBe(0);
    Storage::disk('public')->assertMissing($media->path);
});
