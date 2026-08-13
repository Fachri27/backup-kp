---
paths:
  - 'resources/views/**'
---

# Views

## Leaflet in Livewire/Alpine views must teardown before re-init
Leaflet stamps `el._leaflet_id` on its container. Livewire/Flux re-run Alpine's `x-init`, so calling `L.map(el)` twice throws "Map container is already initialized" and then "Invalid LatLng object: (undefined, undefined)". In any Leaflet init: guard `if (!el || typeof L === 'undefined') return;`, then `try { if (el._leaflet_id != null) L.getMap(el).remove(); } catch(e) { el._leaflet_id = null; el.innerHTML = ''; }` before `L.map(el)`. Also load leaflet.js in <head> (or before Alpine starts) and skip markers where `typeof lat/lng !== 'number'`. See resources/views/livewire/cms/platform-map.blade.php and resources/views/platform-map.blade.php.
