<?php

namespace App\Console\Commands;

use App\Services\MetaService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('meta:test-connection')]
#[Description('Verify Facebook Page and Instagram Business Account API connection via Meta Graph API')]
class MetaTestConnectionCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(MetaService $meta): int
    {
        $this->info('===================================================');
        $this->info('  Meta Graph API Connection Diagnostics (Facebook & Instagram)  ');
        $this->info('===================================================');

        if (! $meta->isConfigured()) {
            $this->error('Error: META_PAGE_ACCESS_TOKEN is not configured in your .env file.');
            $this->line('Please add the following variables to your .env:');
            $this->comment('META_PAGE_ACCESS_TOKEN=EAAG...');
            $this->comment('META_PAGE_ID=123456789 (optional, defaults to "me")');
            $this->comment('META_INSTAGRAM_ACCOUNT_ID=1784... (optional, auto-detected from page)');

            return self::FAILURE;
        }

        // 1. Test Facebook Page
        $this->newLine();
        $this->line('1. Testing Facebook Page connection...');
        $fbResult = $meta->testFacebookConnection();

        if ($fbResult['success']) {
            $page = $fbResult['page'];
            $this->info('  [OK] Facebook Page connected successfully!');
            $this->table(
                ['Property', 'Value'],
                [
                    ['Page Name', $page['name'] ?? 'N/A'],
                    ['Page ID', $page['id'] ?? 'N/A'],
                    ['Category', $page['category'] ?? 'N/A'],
                    ['Followers / Fans', (string) ($page['fan_count'] ?? 0)],
                    ['Link', $page['link'] ?? 'N/A'],
                ]
            );
        } else {
            $this->error('  [FAILED] Facebook Page connection error:');
            $this->error('  '.$fbResult['error']);
        }

        // 2. Test Instagram Business Account
        $this->newLine();
        $this->line('2. Testing Instagram Business Account connection...');
        $igResult = $meta->testInstagramConnection();

        if ($igResult['success']) {
            $ig = $igResult['instagram'];
            $this->info('  [OK] Instagram Business Account connected successfully!');
            $this->table(
                ['Property', 'Value'],
                [
                    ['Username', '@'.($ig['username'] ?? 'N/A')],
                    ['Account Name', $ig['name'] ?? 'N/A'],
                    ['Instagram ID', $ig['id'] ?? 'N/A'],
                    ['Followers', (string) ($ig['followers_count'] ?? 0)],
                ]
            );
        } else {
            $this->warn('  [NOTICE] Instagram connection warning:');
            $this->warn('  '.$igResult['error']);
        }

        $this->newLine();
        $this->info('Diagnostics completed.');

        return ($fbResult['success'] || $igResult['success']) ? self::SUCCESS : self::FAILURE;
    }
}
