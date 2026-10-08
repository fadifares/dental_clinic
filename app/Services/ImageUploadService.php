<?php

namespace App\Services;

use GdImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;
use SplFileInfo;

/**
 * Service for uploading, auto-orienting, resizing, and compressing images.
 * Specially designed for receipts, expense invoices, and InstaPay / bank transfer screenshots.
 */
class ImageUploadService
{
    /**
     * Default maximum width for receipt and invoice images.
     */
    public const DEFAULT_MAX_WIDTH = 1600;

    /**
     * Default maximum height for receipt and invoice images.
     */
    public const DEFAULT_MAX_HEIGHT = 1600;

    /**
     * Default compression quality (82% provides imperceptible quality loss with massive size savings).
     */
    public const DEFAULT_QUALITY = 82;

    /**
     * Upload an image file, compress it while preserving crystal-clear visual quality, and store it.
     *
     * @param  UploadedFile|SplFileInfo|string  $file  Uploaded file instance or local path.
     * @param  string  $directory  Storage subdirectory (e.g. 'expenses', 'receipts', 'transfers').
     * @param  int  $maxWidth  Maximum width in pixels (will not upscale).
     * @param  int  $maxHeight  Maximum height in pixels (will not upscale).
     * @param  int  $quality  Compression quality (1-100). Default is 82.
     * @param  string  $format  Target format: 'webp', 'jpeg', 'png', or 'original'. Default is 'webp'.
     * @param  string  $disk  Filesystem disk (default: 'public').
     *
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public static function upload(
        UploadedFile|SplFileInfo|string $file,
        string $directory = 'receipts',
        int $maxWidth = self::DEFAULT_MAX_WIDTH,
        int $maxHeight = self::DEFAULT_MAX_HEIGHT,
        int $quality = self::DEFAULT_QUALITY,
        string $format = 'webp',
        string $disk = 'public'
    ): CompressedImage {
        $realPath = self::resolveRealPath($file);
        $originalName = self::resolveOriginalName($file);
        $originalSize = (int) filesize($realPath);

        if ($originalSize === 0) {
            throw new InvalidArgumentException('الملف المرفوع فارغ أو تعذر الوصول إليه.');
        }

        $imageInfo = @getimagesize($realPath);
        if ($imageInfo === false) {
            throw new InvalidArgumentException('الملف المحدد ليس ملف صورة صالح.');
        }

        [$width, $height, $imageType] = $imageInfo;

        $sourceGd = self::createGdResource($realPath, $imageType);
        if (! $sourceGd instanceof GdImage) {
            throw new RuntimeException('تعذر معالجة وقراءة بيانات الصورة.');
        }

        // Correct orientation based on EXIF metadata (camera shots / smartphone receipts)
        $sourceGd = self::autoRotateFromExif($sourceGd, $realPath, $imageType);
        $width = imagesx($sourceGd);
        $height = imagesy($sourceGd);

        // Calculate proportional dimensions without stretching or upscaling
        [$targetWidth, $targetHeight] = self::calculateDimensions($width, $height, $maxWidth, $maxHeight);

        // Resample image
        $targetGd = self::resampleImage($sourceGd, $width, $height, $targetWidth, $targetHeight);

        // Determine output format and encode
        $targetFormat = strtolower($format);
        if ($targetFormat === 'original') {
            $targetFormat = self::detectFormatFromType($imageType);
        }

        if ($targetFormat === 'webp' && ! function_exists('imagewebp')) {
            $targetFormat = 'jpeg';
        }

        [$encodedData, $extension, $mimeType] = self::encodeImage($targetGd, $targetFormat, $quality);

        // Free GD memory
        imagedestroy($sourceGd);
        imagedestroy($targetGd);

        // Store file securely in configured disk
        $filename = Str::random(40).'.'.$extension;
        $cleanDir = trim($directory, '/\\');
        $storagePath = ($cleanDir !== '' ? $cleanDir.'/' : '').date('Y/m').'/'.$filename;

        Storage::disk($disk)->put($storagePath, $encodedData);

        $compressedSize = strlen($encodedData);
        $savedBytes = max(0, $originalSize - $compressedSize);
        $savingsPercentage = $originalSize > 0
            ? round(($savedBytes / $originalSize) * 100, 2)
            : 0.0;

        $publicUrl = Storage::disk($disk)->url($storagePath);

        return new CompressedImage(
            path: $storagePath,
            url: $publicUrl,
            filename: $filename,
            directory: $cleanDir,
            disk: $disk,
            originalName: $originalName,
            originalSize: $originalSize,
            compressedSize: $compressedSize,
            savedBytes: $savedBytes,
            savingsPercentage: $savingsPercentage,
            mimeType: $mimeType,
            width: $targetWidth,
            height: $targetHeight,
            format: $targetFormat,
        );
    }

    /**
     * Specialized helper for uploading financial receipts, expense invoices,
     * and bank/InstaPay transfer screenshots.
     *
     * @param  string  $directory  e.g. 'expenses', 'transfers/instapay', 'invoices/receipts'
     * @param  array{
     *     max_width?: int,
     *     max_height?: int,
     *     quality?: int,
     *     format?: string,
     *     disk?: string
     * }  $options
     */
    public static function uploadReceipt(
        UploadedFile|SplFileInfo|string $file,
        string $directory = 'receipts/expenses',
        array $options = []
    ): CompressedImage {
        return self::upload(
            file: $file,
            directory: $directory,
            maxWidth: $options['max_width'] ?? 1920,
            maxHeight: $options['max_height'] ?? 1920,
            quality: $options['quality'] ?? 85,
            format: $options['format'] ?? 'webp',
            disk: $options['disk'] ?? 'public',
        );
    }

    /**
     * Delete an image from storage if it exists.
     */
    public static function delete(?string $path, string $disk = 'public'): bool
    {
        if (blank($path)) {
            return false;
        }

        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->delete($path);
        }

        return false;
    }

    /**
     * Check if an image exists in storage.
     */
    public static function exists(?string $path, string $disk = 'public'): bool
    {
        if (blank($path)) {
            return false;
        }

        return Storage::disk($disk)->exists($path);
    }

    /**
     * Get the URL for a stored image path.
     */
    public static function url(?string $path, string $disk = 'public'): ?string
    {
        if (blank($path)) {
            return null;
        }

        return Storage::disk($disk)->url($path);
    }

    /**
     * Resolve the filesystem real path of the input file.
     */
    protected static function resolveRealPath(UploadedFile|SplFileInfo|string $file): string
    {
        if ($file instanceof UploadedFile) {
            if (! $file->isValid()) {
                throw new InvalidArgumentException('فشل رفع الملف أو الملف غير صالح: '.$file->getErrorMessage());
            }

            return $file->getRealPath();
        }

        if ($file instanceof SplFileInfo) {
            return $file->getRealPath();
        }

        if (! file_exists($file)) {
            throw new InvalidArgumentException("مسار الملف غير موجود: {$file}");
        }

        return realpath($file) ?: $file;
    }

    /**
     * Resolve original file name.
     */
    protected static function resolveOriginalName(UploadedFile|SplFileInfo|string $file): ?string
    {
        if ($file instanceof UploadedFile) {
            return $file->getClientOriginalName();
        }

        if ($file instanceof SplFileInfo) {
            return $file->getFilename();
        }

        return basename($file);
    }

    /**
     * Create GD resource from file path and image type.
     */
    protected static function createGdResource(string $path, int $imageType): ?GdImage
    {
        return match ($imageType) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($path) ?: null,
            IMAGETYPE_PNG => @imagecreatefrompng($path) ?: null,
            IMAGETYPE_WEBP => @imagecreatefromwebp($path) ?: null,
            IMAGETYPE_GIF => @imagecreatefromgif($path) ?: null,
            IMAGETYPE_BMP => @imagecreatefrombmp($path) ?: null,
            default => null,
        };
    }

    /**
     * Automatically adjust image rotation according to EXIF orientation.
     */
    protected static function autoRotateFromExif(GdImage $image, string $path, int $imageType): GdImage
    {
        if ($imageType !== IMAGETYPE_JPEG || ! function_exists('exif_read_data')) {
            return $image;
        }

        $exif = @exif_read_data($path);
        if (! is_array($exif) || ! isset($exif['Orientation'])) {
            return $image;
        }

        $orientation = (int) $exif['Orientation'];

        return match ($orientation) {
            3 => imagerotate($image, 180, 0),
            6 => imagerotate($image, -90, 0),
            8 => imagerotate($image, 90, 0),
            default => $image,
        };
    }

    /**
     * Calculate proportional dimensions keeping aspect ratio without upscaling.
     *
     * @return array{0: int, 1: int}
     */
    protected static function calculateDimensions(int $width, int $height, int $maxWidth, int $maxHeight): array
    {
        if ($width <= $maxWidth && $height <= $maxHeight) {
            return [$width, $height];
        }

        $ratio = min($maxWidth / $width, $maxHeight / $height);

        $targetWidth = (int) max(1, round($width * $ratio));
        $targetHeight = (int) max(1, round($height * $ratio));

        return [$targetWidth, $targetHeight];
    }

    /**
     * High-quality resampling preserving transparency.
     */
    protected static function resampleImage(
        GdImage $source,
        int $srcWidth,
        int $srcHeight,
        int $targetWidth,
        int $targetHeight
    ): GdImage {
        $target = imagecreatetruecolor($targetWidth, $targetHeight);

        // Preserve alpha transparency for PNG & WebP
        imagealphablending($target, false);
        imagesavealpha($target, true);
        $transparent = imagecolorallocatealpha($target, 255, 255, 255, 127);
        imagefilledrectangle($target, 0, 0, $targetWidth, $targetHeight, $transparent);

        imagecopyresampled(
            $target,
            $source,
            0,
            0,
            0,
            0,
            $targetWidth,
            $targetHeight,
            $srcWidth,
            $srcHeight
        );

        return $target;
    }

    /**
     * Detect format string from GD image type constant.
     */
    protected static function detectFormatFromType(int $imageType): string
    {
        return match ($imageType) {
            IMAGETYPE_PNG => 'png',
            IMAGETYPE_WEBP => 'webp',
            default => 'jpeg',
        };
    }

    /**
     * Encode GD image to the specified format and return binary data, extension, and mime.
     *
     * @return array{0: string, 1: string, 2: string}
     */
    protected static function encodeImage(GdImage $image, string $format, int $quality): array
    {
        $quality = max(10, min(100, $quality));
        ob_start();

        switch ($format) {
            case 'webp':
                imagewebp($image, null, $quality);
                $extension = 'webp';
                $mime = 'image/webp';
                break;

            case 'png':
                // PNG compression ranges from 0 (no compression) to 9 (maximum compression)
                $pngQuality = (int) round((100 - $quality) / 11);
                $pngQuality = max(0, min(9, $pngQuality));
                imagepng($image, null, $pngQuality);
                $extension = 'png';
                $mime = 'image/png';
                break;

            case 'jpeg':
            case 'jpg':
            default:
                imagejpeg($image, null, $quality);
                $extension = 'jpg';
                $mime = 'image/jpeg';
                break;
        }

        $data = (string) ob_get_clean();

        return [$data, $extension, $mime];
    }
}
