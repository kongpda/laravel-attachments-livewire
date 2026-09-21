# Laravel Attachments Livewire

[![Tests](https://github.com/kongpda/laravel-attachments-livewire/actions/workflows/run-tests.yml/badge.svg)](https://github.com/kongpda/laravel-attachments-livewire/actions/workflows/run-tests.yml)

A Livewire attachments section for
[`kongpda/laravel-attachments-core`](https://github.com/kongpda/laravel-attachments-core).
It handles upload, preview, captions and delete, and comes in a plain Tailwind
driver and a [Flux](https://fluxui.dev) driver.

Storage, routes and authorisation belong to the core package. Read its README
first.

## Requirements

- PHP 8.4+
- Laravel 12 or 13
- Livewire 3 or 4
- `livewire/flux` if you use the `flux` driver

## Installation

```bash
composer require kongpda/laravel-attachments-livewire
```

This installs the core package too. Follow its installation steps: migrations,
the policy, and the prune schedule.

## Usage

Pass the parent model and its attachments as resources:

```blade
<livewire:attachments-section
    :attachable="$invoice"
    :attachments="\Kongpda\LaravelAttachments\Http\Resources\AttachmentResource::collection($invoice->attachments)->resolve()"
    :allow-upload="true"
/>
```

| Prop | Default | Purpose |
| --- | --- | --- |
| `attachable` | required | The model that owns the attachments. |
| `attachments` | `[]` | `AttachmentResource` arrays to show. |
| `allowUpload` | `false` | Show the upload dropzone. |
| `editable` | `true` | Allow caption edits. |
| `showDelete` | `true` | Show the delete action. |
| `showPreview` | `true` | Show the preview action. |
| `policy` | `'update'` | Ability checked on `attachable` before any change. |

Every change (an upload, a caption, a delete) is authorised against
`attachable` with `policy`. A caption or delete also checks that the
attachment belongs to `attachable`. All of these props are `#[Locked]`: a
visitor cannot switch on uploads or swap the ability from the browser.

## UI drivers

Choose the driver in the core config, or with the environment:

```dotenv
ATTACHMENTS_UI_DRIVER=flux   # or "default"
```

The `default` driver uses plain Tailwind classes. Add the package views to
your Tailwind sources so its classes are generated:

```css
/* resources/css/app.css (Tailwind v4) */
@source '../../vendor/kongpda/laravel-attachments-livewire/resources/views';
```

## Customising

The views and translations use the `laravel-attachments` namespace. Publish
them to edit a copy:

```bash
php artisan vendor:publish --tag=attachments-livewire-views
php artisan vendor:publish --tag=attachments-livewire-translations
```

They land in `resources/views/vendor/laravel-attachments/` and
`lang/vendor/laravel-attachments/`. Delete any file you did not change, so it
keeps getting updates.

For example, `resources/views/vendor/laravel-attachments/livewire/attachment-section.blade.php`.

To register the component under another name, set
`attachments.livewire.register_components` to `false` and register
`Kongpda\LaravelAttachments\Livewire\AttachmentSection` yourself.

## Security

See [SECURITY.md](SECURITY.md). The PDF preview iframe is not sandboxed,
because browsers refuse to render PDFs in sandboxed frames. It is safe
because the core download route serves only images and PDFs inline, and sends
everything else as a download with `nosniff`. Keep uploads behind that route.

## Testing

```bash
composer test
composer analyse
```

## Changelog

See [CHANGELOG.md](CHANGELOG.md).

## Credits

- [kongpda](https://github.com/kongpda)
- Scaffolded from [spatie/package-skeleton-laravel](https://github.com/spatie/package-skeleton-laravel), and built on [spatie/laravel-package-tools](https://github.com/spatie/laravel-package-tools).

## License

MIT. See [LICENSE.md](LICENSE.md).
