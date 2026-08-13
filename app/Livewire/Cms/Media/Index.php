<?php

namespace App\Livewire\Cms\Media;

use App\Models\Media;
use Flux\Flux;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Number;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

/**
 * Livewire exposes #[Computed] methods as properties at runtime.
 *
 * @property-read Collection<int, Media> $files
 * @property-read Media|null $selected
 * @property-read string $usedSpace
 */
#[Layout('layouts::cms')]
#[Title('Media Library')]
class Index extends Component
{
    use WithFileUploads;

    /** @var array<int, TemporaryUploadedFile> */
    public array $uploads = [];

    public ?int $selectedId = null;

    public string $view = 'grid';

    /**
     * @return Collection<int, Media>
     */
    #[Computed]
    public function files(): Collection
    {
        return Media::query()->latest()->get();
    }

    #[Computed]
    public function selected(): ?Media
    {
        return $this->selectedId === null
            ? null
            : $this->files->firstWhere('id', $this->selectedId);
    }

    #[Computed]
    public function usedSpace(): string
    {
        return Number::fileSize((int) $this->files->sum('size'), precision: 1);
    }

    /**
     * Persist files as soon as they are dropped or picked.
     */
    public function updatedUploads(): void
    {
        $this->validate([
            'uploads.*' => ['file', 'image', 'max:5120'],
        ]);

        foreach ($this->uploads as $upload) {
            $this->store($upload);
        }

        $count = count($this->uploads);
        $this->uploads = [];

        unset($this->files);

        Flux::toast(variant: 'success', text: trans_choice('Uploaded :count file.|Uploaded :count files.', $count, ['count' => $count]));
    }

    public function select(int $id): void
    {
        $this->selectedId = $this->selectedId === $id ? null : $id;
    }

    public function delete(int $id): void
    {
        $media = Media::query()->find($id);

        if ($media === null) {
            return;
        }

        Storage::disk($media->disk)->delete($media->path);

        $media->delete();

        $this->selectedId = null;

        unset($this->files);

        Flux::toast(variant: 'success', text: __('File deleted.'));
    }

    private function store(TemporaryUploadedFile $upload): void
    {
        $path = $upload->store('media', 'public');

        [$width, $height] = $this->dimensions($upload);

        Media::query()->create([
            'name' => $upload->getClientOriginalName(),
            'path' => $path,
            'disk' => 'public',
            'mime_type' => $upload->getMimeType(),
            'size' => $upload->getSize(),
            'width' => $width,
            'height' => $height,
            'user_id' => Auth::id(),
        ]);
    }

    /**
     * @return array{0: int|null, 1: int|null}
     */
    private function dimensions(TemporaryUploadedFile $upload): array
    {
        $size = @getimagesize($upload->getRealPath());

        return $size === false
            ? [null, null]
            : [$size[0], $size[1]];
    }
}
