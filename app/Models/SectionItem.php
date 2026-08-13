<?php

namespace App\Models;

use Database\Factories\SectionItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * One repeatable row inside a page section - a feature card, a stat tile, a map
 * callout or a nav link. The shape of `data` varies by `type`.
 *
 * @property int $id
 * @property int $page_section_id
 * @property string $type
 * @property int $sort_order
 * @property bool $is_published
 * @property array<string, mixed>|null $data
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['page_section_id', 'type', 'sort_order', 'is_published', 'data'])]
class SectionItem extends Model
{
    /** @use HasFactory<SectionItemFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'data' => 'array',
            'is_published' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<PageSection, $this>
     */
    public function pageSection(): BelongsTo
    {
        return $this->belongsTo(PageSection::class);
    }

    /**
     * @param  Builder<$this>  $query
     */
    public function scopePublished(Builder $query): void
    {
        $query->where('is_published', true);
    }

    /**
     * Read a value out of the item's data blob.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return data_get($this->data, $key, $default);
    }
}
