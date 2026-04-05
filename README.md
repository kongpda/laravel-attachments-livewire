# Kongpda Laravel Attachments Livewire

Blade and Livewire UI for the Kongpda attachments ecosystem.

This package depends on `kongpda/laravel-attachments-core` and provides:

- `AttachmentSection`
- `AttachmentEditCard`
- `x-attachments.*` Blade components
- `x-attachments::*` namespaced Blade components
- `default` and `flux` UI drivers

This package does not own storage, routing, or backend attachment rules. Those stay in `kongpda/laravel-attachments-core`.

## Local development

For package-local development, keep the split repositories beside each other:

- `../laravel-attachments-core`
- `../laravel-attachments-livewire`

Then run:

```bash
composer install
vendor/bin/pest
```

The local path repository in `composer.json` lets this package resolve the sibling core checkout without going through Packagist.
