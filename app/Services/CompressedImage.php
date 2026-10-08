<?php

namespace App\Services;

use ArrayAccess;
use JsonSerializable;
use Stringable;

/**
 * Value object representing a successfully uploaded and compressed image.
 *
 * Implements Stringable, ArrayAccess, and JsonSerializable for maximum flexibility:
 * - Can be cast to string to obtain the relative storage path.
 * - Can be accessed like an array: $result['path'], $result['url'].
 * - Can be accessed like an object: $result->path, $result->url.
 */
class CompressedImage implements ArrayAccess, JsonSerializable, Stringable
{
    /**
     * @param  array{
     *     path: string,
     *     url: string,
     *     filename: string,
     *     directory: string,
     *     disk: string,
     *     original_name: ?string,
     *     original_size: int,
     *     compressed_size: int,
     *     saved_bytes: int,
     *     savings_percentage: float,
     *     mime_type: string,
     *     width: int,
     *     height: int,
     *     format: string
     * }  $attributes
     */
    public function __construct(
        public readonly string $path,
        public readonly string $url,
        public readonly string $filename,
        public readonly string $directory,
        public readonly string $disk,
        public readonly ?string $originalName,
        public readonly int $originalSize,
        public readonly int $compressedSize,
        public readonly int $savedBytes,
        public readonly float $savingsPercentage,
        public readonly string $mimeType,
        public readonly int $width,
        public readonly int $height,
        public readonly string $format,
        protected readonly array $attributes = [],
    ) {}

    /**
     * Get the relative storage path when cast to string.
     */
    public function __toString(): string
    {
        return $this->path;
    }

    /**
     * Get array representation of image metadata.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'path' => $this->path,
            'url' => $this->url,
            'filename' => $this->filename,
            'directory' => $this->directory,
            'disk' => $this->disk,
            'original_name' => $this->originalName,
            'original_size' => $this->originalSize,
            'compressed_size' => $this->compressedSize,
            'saved_bytes' => $this->savedBytes,
            'savings_percentage' => $this->savingsPercentage,
            'mime_type' => $this->mimeType,
            'width' => $this->width,
            'height' => $this->height,
            'format' => $this->format,
        ];
    }

    /**
     * Serialize to JSON.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->toArray()[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->toArray()[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        // Read-only object
    }

    public function offsetUnset(mixed $offset): void
    {
        // Read-only object
    }
}
