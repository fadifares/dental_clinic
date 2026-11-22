<?php

namespace App\Console\Commands;

use App\Services\MetaService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('meta:comments 
    {--platform=facebook : Target platform: "facebook" or "instagram"}
    {--limit=5 : Number of posts to check}
    {--reply-to= : Comment ID to reply to}
    {--message= : Reply message}
')]
#[Description('View recent comments or reply to a comment on Facebook Page or Instagram')]
class MetaManageCommentsCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(MetaService $meta): int
    {
        $platform = strtolower((string) $this->option('platform'));
        $replyTo = $this->option('reply-to');
        $message = $this->option('message');

        // Handle Reply Action
        if (! empty($replyTo)) {
            if (empty($message)) {
                $this->error('Error: You must provide --message="your reply" when replying to a comment.');

                return self::FAILURE;
            }

            $this->info("Replying to {$platform} comment [{$replyTo}]...");
            $res = ($platform === 'instagram')
                ? $meta->replyToInstagramComment($replyTo, $message)
                : $meta->replyToFacebookComment($replyTo, $message);

            if ($res['success']) {
                $this->info("  [SUCCESS] Reply published successfully! ID: {$res['comment_id']}");

                return self::SUCCESS;
            }

            $this->error('  [FAILED] Could not post reply: '.$res['error']);

            return self::FAILURE;
        }

        // List Comments Action
        $limit = (int) $this->option('limit');
        $this->info("Fetching latest {$platform} posts and comments (limit: {$limit})...");

        if ($platform === 'instagram') {
            $result = $meta->getInstagramMedia($limit);
            if (! $result['success']) {
                $this->error('Failed to fetch Instagram media: '.$result['error']);

                return self::FAILURE;
            }

            $mediaItems = $result['media'] ?? [];
            if (empty($mediaItems)) {
                $this->line('No Instagram media found.');

                return self::SUCCESS;
            }

            foreach ($mediaItems as $media) {
                $caption = mb_substr((string) ($media['caption'] ?? 'No Caption'), 0, 40);
                $this->newLine();
                $this->line("Media [{$media['id']}]: {$caption}...");
                $comments = $media['comments']['data'] ?? [];

                if (empty($comments)) {
                    $this->comment('  (No comments yet)');
                } else {
                    foreach ($comments as $c) {
                        $this->info("  - Comment [{$c['id']}] by @{$c['username']}: {$c['text']}");
                    }
                }
            }
        } else {
            $result = $meta->getFacebookPosts($limit);
            if (! $result['success']) {
                $this->error('Failed to fetch Facebook posts: '.$result['error']);

                return self::FAILURE;
            }

            $posts = $result['posts'] ?? [];
            if (empty($posts)) {
                $this->line('No Facebook posts found.');

                return self::SUCCESS;
            }

            foreach ($posts as $p) {
                $msg = mb_substr((string) ($p['message'] ?? 'No text'), 0, 40);
                $this->newLine();
                $this->line("Post [{$p['id']}]: {$msg}...");
                $comments = $p['comments']['data'] ?? [];

                if (empty($comments)) {
                    $this->comment('  (No comments yet)');
                } else {
                    foreach ($comments as $c) {
                        $from = $c['from']['name'] ?? 'User';
                        $this->info("  - Comment [{$c['id']}] by {$from}: {$c['message']}");
                    }
                }
            }
        }

        $this->newLine();
        $this->info('To reply to any comment, run:');
        $this->comment("php artisan meta:comments --platform={$platform} --reply-to=\"COMMENT_ID\" --message=\"شكراً لتواصلك معنا! 🦷\"");

        return self::SUCCESS;
    }
}
