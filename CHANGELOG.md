# Changelog

## Unreleased

## 0.2.0 - 2026-09-21

### Security

- Component settings (`attachable`, `policy`, `allowUpload`, `editable`,
  `showDelete`, `showPreview`) are `#[Locked]`.
- Uploads always check the configured ability on the parent.
- Replaced inline `onerror` handlers with Alpine `x-on:error`, so a strict
  Content Security Policy does not block them.

### Changed

- Requires `kongpda/laravel-attachments-core` ^0.2, PHP 8.4, and Laravel 12
  or 13.
- Supports Livewire 3 and 4.
- The default driver's text is translatable.

### Fixed

- `vendor:publish --tag=attachments-livewire-views` and
  `--tag=attachments-livewire-translations` now copy files to
  `vendor/laravel-attachments`, where overrides are read from.

### Removed

- The unused `preview-card` and `pending-card` Blade components (all drivers)
  and `Support\AttachmentUi`. Breaking if you rendered them directly.

## 0.1.0 - 2026-04-05

- Initial release.
