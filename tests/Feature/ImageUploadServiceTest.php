<?php

namespace Tests\Feature;

use App\Services\CompressedImage;
use App\Services\ImageUploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Tests\TestCase;

class ImageUploadServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_it_compresses_and_uploads_large_image_proportionally(): void
    {
        $fakeFile = UploadedFile::fake()->image('large_receipt.jpg', 3000, 2000);

        $result = ImageUploadService::upload(
            file: $fakeFile,
            directory: 'receipts/expenses',
            maxWidth: 1600,
            maxHeight: 1600,
            quality: 82,
            format: 'webp',
            disk: 'public'
        );

        $this->assertInstanceOf(CompressedImage::class, $result);
        $this->assertTrue(Storage::disk('public')->exists($result->path));
        $this->assertLessThanOrEqual(1600, $result->width);
        $this->assertLessThanOrEqual(1600, $result->height);
        $this->assertSame('webp', $result->format);
        $this->assertSame('image/webp', $result->mimeType);
        $this->assertStringStartsWith('receipts/expenses/', $result->path);
        $this->assertStringEndsWith('.webp', $result->path);

        // Can access via object property, array access, and string cast
        $this->assertSame($result->path, $result['path']);
        $this->assertSame($result->path, (string) $result);
        $this->assertNotEmpty($result->url);
    }

    public function test_it_supports_upload_receipt_specialized_preset_for_instapay(): void
    {
        $fakeFile = UploadedFile::fake()->image('instapay_screenshot.png', 1080, 1920);

        $result = ImageUploadService::uploadReceipt($fakeFile, 'transfers/instapay');

        $this->assertTrue(Storage::disk('public')->exists($result->path));
        $this->assertStringStartsWith('transfers/instapay/', $result->path);
        $this->assertSame(1080, $result->width); // Did not upscale or shrink inappropriately
        $this->assertSame(1920, $result->height);
        $this->assertGreaterThan(0, $result->compressedSize);
    }

    public function test_it_supports_jpeg_and_png_formats(): void
    {
        $fakeFile = UploadedFile::fake()->image('invoice.png', 800, 600);

        $result = ImageUploadService::upload(
            file: $fakeFile,
            directory: 'invoices',
            format: 'jpeg',
            disk: 'public'
        );

        $this->assertTrue(Storage::disk('public')->exists($result->path));
        $this->assertSame('jpg', pathinfo($result->path, PATHINFO_EXTENSION));
        $this->assertSame('image/jpeg', $result->mimeType);
    }

    public function test_it_can_delete_uploaded_image(): void
    {
        $fakeFile = UploadedFile::fake()->image('temp.jpg', 200, 200);
        $result = ImageUploadService::upload($fakeFile, 'temp');

        $this->assertTrue(ImageUploadService::exists($result->path));

        $deleted = ImageUploadService::delete($result->path);
        $this->assertTrue($deleted);
        $this->assertFalse(ImageUploadService::exists($result->path));
    }

    public function test_it_rejects_non_image_files(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $fakePdf = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');
        ImageUploadService::upload($fakePdf);
    }
}
