<?php

namespace App\Models;

use App\Enums\SectionStatus;
use Database\Factories\PageSectionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * A single editable band of the public landing page (hero, about, stats, ...).
 *
 * Free-form copy lives in `content`; repeatable rows (feature cards, stat tiles,
 * map callouts, nav links) live in the related section items.
 *
 * @property int $id
 * @property string $key
 * @property string $name
 * @property string|null $description
 * @property array<string, mixed>|null $content
 * @property SectionStatus $status
 * @property int $sort_order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['key', 'name', 'description', 'content', 'status', 'sort_order'])]
class PageSection extends Model
{
    /** @use HasFactory<PageSectionFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'content' => 'array',
            'status' => SectionStatus::class,
            'sort_order' => 'integer',
        ];
    }

    /**
     * Route sections by their stable key, so CMS URLs read /cms/content/hero.
     */
    public function getRouteKeyName(): string
    {
        return 'key';
    }

    /**
     * @return HasMany<SectionItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(SectionItem::class)->orderBy('sort_order');
    }

    /**
     * @param  Builder<$this>  $query
     */
    public function scopePublished(Builder $query): void
    {
        $query->where('status', SectionStatus::Published);
    }

    public function isPublished(): bool
    {
        return $this->status === SectionStatus::Published;
    }

    /**
     * Read a dot-notated value out of the section's content blob.
     */
    public function copy(string $key, mixed $default = null): mixed
    {
        return data_get($this->content, $key, $default);
    }

    /**
     * The published items of a given type, in order.
     *
     * @return Collection<int, SectionItem>
     */
    public function publishedItems(string $type): Collection
    {
        return $this->items
            ->where('type', $type)
            ->where('is_published', true)
            ->values();
    }
}
