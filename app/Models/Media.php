<?php

namespace App\Models;

use Database\Factories\MediaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Number;

/**
 * An uploaded file in the CMS media library.
 *
 * @property int $id
 * @property string $name
 * @property string $path
 * @property string $disk
 * @property string|null $mime_type
 * @property int $size
 * @property int|null $width
 * @property int|null $height
 * @property int|null $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string $url
 * @property-read string $human_size
 */
#[Fillable(['name', 'path', 'disk', 'mime_type', 'size', 'width', 'height', 'user_id'])]
class Media extends Model
{
    /** @use HasFactory<MediaFactory> */
    use HasFactory;

    protected $table = 'media';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'size' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return Attribute<string, never>
     */
    protected function url(): Attribute
    {
        return Attribute::get(fn (): string => Storage::disk($this->disk)->url($this->path));
    }

    /**
     * @return Attribute<string, never>
     */
    protected function humanSize(): Attribute
    {
        return Attribute::get(fn (): string => Number::fileSize($this->size, precision: 0));
    }

    /**
     * Human-readable pixel dimensions, when they are known.
     */
    public function dimensions(): ?string
    {
        if ($this->width === null || $this->height === null) {
            return null;
        }

        return sprintf('%d×%d', $this->width, $this->height);
    }

    public function isImage(): bool
    {
        return str_starts_with((string) $this->mime_type, 'image/');
    }
}
