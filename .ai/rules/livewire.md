---
paths:
  - 'resources/views/livewire/**'
---

# Livewire

## make:livewire puts Index component views one level too deep
Livewire 4 drops the trailing `Index` when resolving a class-based component's view: `App\Livewire\Cms\Content\Index` resolves to `resources/views/livewire/cms/content.blade.php`, NOT `.../cms/content/index.blade.php`.

`php artisan make:livewire Cms/Content/Index --class` generates the view at the nested `index.blade.php` path, which Livewire then cannot find — you get a FileNotFoundException at render time, not at boot, so it only shows up when you hit the route.

After generating any `*/Index` component, move the view up a level and drop the `index` segment. Sibling non-Index components (e.g. `Content\Edit`) keep the nested path `livewire/cms/content/edit.blade.php`.
