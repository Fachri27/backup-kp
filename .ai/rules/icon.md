---
paths:
  - 'resources/views/flux/icon/**'
---

# Icon

## Importing Lucide icons can silently override Heroicons used elsewhere
`php artisan flux:icon <name>` pulls from Lucide into resources/views/flux/icon/, which takes precedence over Flux's built-in Heroicons app-wide. If the Lucide name collides with a Heroicon already in use (e.g. `chevron-down` in settings pages, `home` in the sidebar), the import silently changes those icons everywhere.

Before importing, grep for the name in resources/views. On a collision, publish under a prefixed filename (e.g. lucide-chevron-down.blade.php) and reference `<flux:icon.lucide-chevron-down />`.

Also note Lucide renames: home -> house, bar-chart-2 -> chart-column-increasing. Brand icons (instagram/twitter/youtube) were dropped from current Lucide and are vendored by hand from lucide-static@0.460.0.

For a runtime icon name use `<flux:icon :icon="$name" />` — `<flux:icon.{{ $var }} />` does not compile.
