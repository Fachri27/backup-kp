<?php

namespace App\Livewire\Cms\Content;

use App\Enums\SectionStatus;
use App\Models\PageSection;
use App\Models\SectionItem;
use Flux\Flux;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Edits one band of the landing page.
 *
 * Sections store free-form copy as JSON and repeatable rows as section items, and
 * the shape differs per section. Rather than hand-writing a schema for each one,
 * the form is derived from the stored data: nested objects are walked into dotted
 * paths, and lists of scalars are edited as comma-separated values.
 */
#[Layout('layouts::cms')]
class Edit extends Component
{
    public PageSection $section;

    /** @var array<int, array{path: string, label: string, type: string, value: mixed}> */
    public array $fields = [];

    /** @var array<int, array{id: int|null, type: string, is_published: bool, fields: array<int, array{path: string, label: string, type: string, value: mixed}>}> */
    public array $items = [];

    public string $status = 'published';

    public function mount(PageSection $section): void
    {
        $this->section = $section;
        $this->status = $section->status->value;
        $this->fields = $this->describe($section->content ?? []);

        foreach ($section->items as $item) {
            $this->items[] = [
                'id' => $item->id,
                'type' => $item->type,
                'is_published' => $item->is_published,
                'fields' => $this->describe($item->data ?? []),
            ];
        }
    }

    public function title(): string
    {
        return $this->section->name;
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    #[Computed]
    public function statuses(): array
    {
        return array_map(
            fn (SectionStatus $status): array => ['value' => $status->value, 'label' => $status->label()],
            SectionStatus::cases(),
        );
    }

    #[Computed]
    public function itemLabel(): string
    {
        $type = $this->items[0]['type'] ?? 'item';

        return Str::headline($type);
    }

    public function save(): void
    {
        $this->validate([
            'status' => ['required', Rule::enum(SectionStatus::class)],
            'fields.*.value' => ['nullable'],
            'items.*.fields.*.value' => ['nullable'],
        ]);

        $this->section->update([
            'content' => $this->rebuild($this->fields),
            'status' => SectionStatus::from($this->status),
        ]);

        foreach ($this->items as $index => $item) {
            if ($item['id'] === null) {
                continue;
            }

            SectionItem::query()->find($item['id'])?->update([
                'is_published' => (bool) $item['is_published'],
                'sort_order' => $index,
                'data' => $this->rebuild($item['fields']),
            ]);
        }

        $this->section->refresh();

        Flux::toast(variant: 'success', text: __('Section saved.'));
    }

    public function publish(): void
    {
        $this->status = SectionStatus::Published->value;

        $this->save();
    }

    /**
     * Append a new row, blanking the values but keeping the shape of the existing rows.
     */
    public function addItem(): void
    {
        $template = $this->items[0] ?? null;

        if ($template === null) {
            return;
        }

        $item = $this->section->items()->create([
            'type' => $template['type'],
            'sort_order' => count($this->items),
            'is_published' => true,
            'data' => $this->rebuild(array_map(
                fn (array $field): array => [...$field, 'value' => $field['type'] === 'bool' ? false : ''],
                $template['fields'],
            )),
        ]);

        $this->items[] = [
            'id' => $item->id,
            'type' => $item->type,
            'is_published' => true,
            'fields' => $this->describe($item->data ?? []),
        ];

        Flux::toast(text: __('Row added.'));
    }

    public function removeItem(int $index): void
    {
        $item = $this->items[$index] ?? null;

        if ($item === null) {
            return;
        }

        if ($item['id'] !== null) {
            SectionItem::query()->find($item['id'])?->delete();
        }

        unset($this->items[$index]);

        $this->items = array_values($this->items);

        Flux::toast(variant: 'success', text: __('Row removed.'));
    }

    public function moveItem(int $index, int $direction): void
    {
        $target = $index + $direction;

        if (! isset($this->items[$index], $this->items[$target])) {
            return;
        }

        [$this->items[$index], $this->items[$target]] = [$this->items[$target], $this->items[$index]];
    }

    /**
     * Walk a stored data blob into a flat list of editable field descriptors.
     *
     * @param  array<string, mixed>  $data
     * @return array<int, array{path: string, label: string, type: string, value: mixed}>
     */
    private function describe(array $data, string $prefix = ''): array
    {
        $fields = [];

        foreach ($data as $key => $value) {
            $path = $prefix === '' ? (string) $key : $prefix.'.'.$key;

            if (is_array($value)) {
                $isScalarList = array_is_list($value)
                    && array_filter($value, 'is_array') === [];

                if ($isScalarList) {
                    $isNumeric = $value !== []
                        && array_filter($value, fn (mixed $entry): bool => ! is_numeric($entry)) === [];

                    $fields[] = [
                        'path' => $path,
                        'label' => $this->label($path),
                        'type' => $isNumeric ? 'numlist' : 'list',
                        'value' => implode(', ', $value),
                    ];

                    continue;
                }

                $fields = [...$fields, ...$this->describe($value, $path)];

                continue;
            }

            if (is_bool($value)) {
                $fields[] = [
                    'path' => $path,
                    'label' => $this->label($path),
                    'type' => 'bool',
                    'value' => $value,
                ];

                continue;
            }

            $fields[] = [
                'path' => $path,
                'label' => $this->label($path),
                'type' => mb_strlen((string) $value) > 80 ? 'textarea' : 'text',
                'value' => (string) $value,
            ];
        }

        return $fields;
    }

    /**
     * Rebuild the nested data blob from the flat field descriptors.
     *
     * @param  array<int, array{path: string, label: string, type: string, value: mixed}>  $fields
     * @return array<string, mixed>
     */
    private function rebuild(array $fields): array
    {
        $data = [];

        foreach ($fields as $field) {
            data_set($data, $field['path'], match ($field['type']) {
                'numlist' => array_map(intval(...), $this->splitList($field['value'])),
                'list' => $this->splitList($field['value']),
                'bool' => (bool) $field['value'],
                default => (string) $field['value'],
            });
        }

        return $data;
    }

    /**
     * @return array<int, string>
     */
    private function splitList(mixed $value): array
    {
        return array_values(array_filter(
            array_map(trim(...), explode(',', (string) $value)),
            fn (string $entry): bool => $entry !== '',
        ));
    }

    private function label(string $path): string
    {
        return Str::headline(str_replace('.', ' ', $path));
    }
}
