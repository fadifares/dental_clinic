<?php

namespace App\Console\Commands;

use App\Services\MetaService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('meta:publish-test 
    {--platform=facebook : Target platform: "facebook", "instagram", or "both"}
    {--message= : Post text/caption}
    {--link= : Optional link URL for Facebook post}
    {--image= : Public HTTPS image URL for Instagram post}
')]
#[Description('Publish a test post to Facebook Page and/or Instagram Business Account')]
class MetaPublishTestCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(MetaService $meta): int
    {
        $platform = strtolower((string) $this->option('platform'));
        $defaultMessage = "تجربة نشر تلقائي من نظام Dental Pro 🦷\nتاريخ: ".now()->format('Y-m-d H:i')."\nhttps://dental.go-2web.com/";
        $message = (string) ($this->option('message') ?: $defaultMessage);
        $link = $this->option('link') ?: 'https://dental.go-2web.com/';
        $imageUrl = (string) $this->option('image');

        $this->info("Initiating test post publication (Platform: {$platform})...");

        $overallSuccess = true;

        // Facebook Publishing
        if (in_array($platform, ['facebook', 'both'], true)) {
            $this->line('Publishing to Facebook Page...');
            $fbRes = $meta->publishFacebookPost($message, $link);

            if ($fbRes['success']) {
                $this->info("  [SUCCESS] Facebook post published! Post ID: {$fbRes['post_id']}");
            } else {
                $overallSuccess = false;
                $this->error('  [FAILED] Facebook post failed: '.$fbRes['error']);
            }
        }

        // Instagram Publishing
        if (in_array($platform, ['instagram', 'both'], true)) {
            $this->line('Publishing to Instagram Business Account...');

            if (empty($imageUrl)) {
                $this->warn('  [SKIPPED] Instagram requires a public HTTPS image URL. Please provide --image="https://example.com/image.jpg"');
            } else {
                $igRes = $meta->publishInstagramPhoto($imageUrl, $message);

                if ($igRes['success']) {
                    $this->info("  [SUCCESS] Instagram media published! Media ID: {$igRes['media_id']}");
                } else {
                    $overallSuccess = false;
                    $this->error('  [FAILED] Instagram post failed: '.$igRes['error']);
                }
            }
        }

        return $overallSuccess ? self::SUCCESS : self::FAILURE;
    }
}
