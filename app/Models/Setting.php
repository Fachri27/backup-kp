<?php

namespace App\Models;

use Database\Factories\SettingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $key
 * @property string $group
 * @property mixed $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['key', 'group', 'value'])]
class Setting extends Model
{
    /** @use HasFactory<SettingFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'value' => 'array',
        ];
    }

    /**
     * Read one setting, falling back when it has never been saved.
     */
    public static function read(string $key, mixed $default = null): mixed
    {
        $setting = static::query()->where('key', $key)->first();

        if ($setting === null) {
            return $default;
        }

        return $setting->value ?? $default;
    }

    /**
     * Create or update one setting.
     */
    public static function write(string $key, mixed $value, string $group = 'general'): void
    {
        static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group],
        );
    }

    /**
     * Every setting in a group, keyed by setting key.
     *
     * @return array<string, mixed>
     */
    public static function group(string $group): array
    {
        return static::query()
            ->where('group', $group)
            ->get()
            ->mapWithKeys(fn (self $setting): array => [$setting->key => $setting->value])
            ->all();
    }
}
