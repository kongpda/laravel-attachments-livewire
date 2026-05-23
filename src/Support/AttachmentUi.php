<?php

declare(strict_types=1);

namespace Kongpda\LaravelAttachments\Support;

use Illuminate\Http\UploadedFile;

final class AttachmentUi
{
    /**
     * @return array<int, string>
     */
    public static function previewableMimes(): array
    {
        return PreviewableMimes::LIST;
    }

    public static function isPreviewableMime(?string $mime): bool
    {
        return PreviewableMimes::isPreviewable($mime);
    }

    public static function isImageUpload(UploadedFile $file): bool
    {
        return str_starts_with((string) $file->getMimeType(), 'image/');
    }

    public static function isPdfUpload(UploadedFile $file): bool
    {
        return $file->getMimeType() === 'application/pdf';
    }

    public static function hasPdfExtension(UploadedFile $file): bool
    {
        $extension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);

        return $extension !== '' && mb_strtolower($extension) === 'pdf';
    }

    public static function resolveTemporaryPreviewUrl(UploadedFile $file): ?string
    {
        if (! self::isImageUpload($file) && ! self::isPdfUpload($file) && ! self::hasPdfExtension($file)) {
            return null;
        }

        try {
            return $file->temporaryUrl();
        } catch (\Throwable) {
            return null;
        }
    }
}
