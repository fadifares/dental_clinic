<?php

namespace App\Services;

use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MetaService
{
    protected string $graphUrl;

    public function __construct(
        protected ?string $pageAccessToken = null,
        protected ?string $pageId = null,
        protected ?string $instagramAccountId = null,
        protected string $graphVersion = 'v21.0'
    ) {
        $this->pageAccessToken = $pageAccessToken ?? (string) config('services.meta.page_access_token');
        $this->pageId = $pageId ?? (string) config('services.meta.page_id');
        $this->instagramAccountId = $instagramAccountId ?? (string) config('services.meta.instagram_account_id');
        $this->graphVersion = $graphVersion ?: ((string) config('services.meta.graph_version') ?: 'v21.0');
        $this->graphUrl = "https://graph.facebook.com/{$this->graphVersion}";
    }

    /**
     * Check whether required Meta credentials are configured.
     */
    public function isConfigured(): bool
    {
        return ! empty($this->pageAccessToken);
    }

    /**
     * Test the Facebook Page connection and fetch page metadata.
     *
     * @return array{success: bool, page?: array, error?: string}
     */
    public function testFacebookConnection(): array
    {
        if (! $this->isConfigured()) {
            return [
                'success' => false,
                'error' => 'META_PAGE_ACCESS_TOKEN is missing in .env configuration.',
            ];
        }

        try {
            $target = ! empty($this->pageId) ? $this->pageId : 'me';
            $response = Http::get("{$this->graphUrl}/{$target}", [
                'fields' => 'id,name,link,fan_count,category',
                'access_token' => $this->pageAccessToken,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'page' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'error' => $this->extractErrorMessage($response),
            ];
        } catch (Exception $e) {
            Log::error('Meta Facebook connection check failed: '.$e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Test the Instagram Business Account connection and fetch account details.
     *
     * @return array{success: bool, instagram?: array, error?: string}
     */
    public function testInstagramConnection(): array
    {
        if (! $this->isConfigured()) {
            return [
                'success' => false,
                'error' => 'META_PAGE_ACCESS_TOKEN is missing in .env configuration.',
            ];
        }

        try {
            $instagramId = $this->instagramAccountId;

            // If Instagram Account ID is not explicitly set, auto-discover it from the connected Facebook Page
            if (empty($instagramId)) {
                $targetPage = ! empty($this->pageId) ? $this->pageId : 'me';
                $pageResponse = Http::get("{$this->graphUrl}/{$targetPage}", [
                    'fields' => 'instagram_business_account{id,username,name,followers_count,profile_picture_url}',
                    'access_token' => $this->pageAccessToken,
                ]);

                if (! $pageResponse->successful()) {
                    return [
                        'success' => false,
                        'error' => 'Failed to query Facebook page for connected Instagram account: '.$this->extractErrorMessage($pageResponse),
                    ];
                }

                $pageData = $pageResponse->json();
                $instagramAccount = $pageData['instagram_business_account'] ?? null;

                if (! $instagramAccount) {
                    return [
                        'success' => false,
                        'error' => 'No Instagram Business Account linked to this Facebook Page. Please link an Instagram Professional/Business account to your Facebook Page in Meta Business Suite.',
                    ];
                }

                return [
                    'success' => true,
                    'instagram' => $instagramAccount,
                ];
            }

            // If ID is explicitly known, fetch directly
            $response = Http::get("{$this->graphUrl}/{$instagramId}", [
                'fields' => 'id,username,name,followers_count,biography,profile_picture_url',
                'access_token' => $this->pageAccessToken,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'instagram' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'error' => $this->extractErrorMessage($response),
            ];
        } catch (Exception $e) {
            Log::error('Meta Instagram connection check failed: '.$e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Publish a text or link post to the Facebook Page feed.
     *
     * @return array{success: bool, post_id?: string, error?: string}
     */
    public function publishFacebookPost(string $message, ?string $link = null): array
    {
        if (! $this->isConfigured()) {
            return [
                'success' => false,
                'error' => 'META_PAGE_ACCESS_TOKEN is missing.',
            ];
        }

        try {
            $target = ! empty($this->pageId) ? $this->pageId : 'me';
            $params = [
                'message' => $message,
                'access_token' => $this->pageAccessToken,
            ];

            if (! empty($link)) {
                $params['link'] = $link;
            }

            $response = Http::asForm()->post("{$this->graphUrl}/{$target}/feed", $params);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'post_id' => $data['id'] ?? 'unknown',
                ];
            }

            return [
                'success' => false,
                'error' => $this->extractErrorMessage($response),
            ];
        } catch (Exception $e) {
            Log::error('Meta Facebook post publishing failed: '.$e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Publish a single photo post to Instagram Business Account.
     * Note: Instagram Graph API requires a publicly accessible HTTPS image URL.
     *
     * @return array{success: bool, media_id?: string, error?: string}
     */
    public function publishInstagramPhoto(string $imageUrl, string $caption): array
    {
        if (! $this->isConfigured()) {
            return [
                'success' => false,
                'error' => 'META_PAGE_ACCESS_TOKEN is missing.',
            ];
        }

        try {
            $igId = $this->resolveInstagramAccountId();
            if (empty($igId)) {
                return [
                    'success' => false,
                    'error' => 'Could not determine Instagram Business Account ID. Please verify your Facebook Page has an attached Instagram Professional Account.',
                ];
            }

            // Step 1: Create media container
            $containerResponse = Http::asForm()->post("{$this->graphUrl}/{$igId}/media", [
                'image_url' => $imageUrl,
                'caption' => $caption,
                'access_token' => $this->pageAccessToken,
            ]);

            if (! $containerResponse->successful()) {
                return [
                    'success' => false,
                    'error' => 'Step 1 (Create Container) failed: '.$this->extractErrorMessage($containerResponse),
                ];
            }

            $creationId = $containerResponse->json('id');
            if (empty($creationId)) {
                return [
                    'success' => false,
                    'error' => 'Container creation did not return a valid creation ID.',
                ];
            }

            // Brief pause to allow Meta's container rendering
            sleep(2);

            // Step 2: Publish the media container
            $publishResponse = Http::asForm()->post("{$this->graphUrl}/{$igId}/media_publish", [
                'creation_id' => $creationId,
                'access_token' => $this->pageAccessToken,
            ]);

            if ($publishResponse->successful()) {
                return [
                    'success' => true,
                    'media_id' => $publishResponse->json('id'),
                ];
            }

            return [
                'success' => false,
                'error' => 'Step 2 (Media Publish) failed: '.$this->extractErrorMessage($publishResponse),
            ];
        } catch (Exception $e) {
            Log::error('Meta Instagram photo publishing failed: '.$e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get recent Facebook posts along with their comments.
     *
     * @return array{success: bool, posts?: array, error?: string}
     */
    public function getFacebookPosts(int $limit = 10): array
    {
        if (! $this->isConfigured()) {
            return ['success' => false, 'error' => 'META_PAGE_ACCESS_TOKEN is missing.'];
        }

        try {
            $target = ! empty($this->pageId) ? $this->pageId : 'me';
            $response = Http::get("{$this->graphUrl}/{$target}/posts", [
                'fields' => 'id,message,created_time,comments{id,message,from,created_time}',
                'limit' => $limit,
                'access_token' => $this->pageAccessToken,
            ]);

            if ($response->successful()) {
                return ['success' => true, 'posts' => $response->json('data', [])];
            }

            return ['success' => false, 'error' => $this->extractErrorMessage($response)];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Reply to a Facebook comment.
     *
     * @return array{success: bool, comment_id?: string, error?: string}
     */
    public function replyToFacebookComment(string $commentId, string $message): array
    {
        if (! $this->isConfigured()) {
            return ['success' => false, 'error' => 'META_PAGE_ACCESS_TOKEN is missing.'];
        }

        try {
            $response = Http::asForm()->post("{$this->graphUrl}/{$commentId}/comments", [
                'message' => $message,
                'access_token' => $this->pageAccessToken,
            ]);

            if ($response->successful()) {
                return ['success' => true, 'comment_id' => $response->json('id')];
            }

            return ['success' => false, 'error' => $this->extractErrorMessage($response)];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Get recent Instagram media along with their comments.
     *
     * @return array{success: bool, media?: array, error?: string}
     */
    public function getInstagramMedia(int $limit = 10): array
    {
        if (! $this->isConfigured()) {
            return ['success' => false, 'error' => 'META_PAGE_ACCESS_TOKEN is missing.'];
        }

        try {
            $igId = $this->resolveInstagramAccountId();
            if (empty($igId)) {
                return ['success' => false, 'error' => 'Instagram Business Account ID not resolved.'];
            }

            $response = Http::get("{$this->graphUrl}/{$igId}/media", [
                'fields' => 'id,caption,media_type,permalink,comments{id,text,username,timestamp}',
                'limit' => $limit,
                'access_token' => $this->pageAccessToken,
            ]);

            if ($response->successful()) {
                return ['success' => true, 'media' => $response->json('data', [])];
            }

            return ['success' => false, 'error' => $this->extractErrorMessage($response)];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Reply to an Instagram comment.
     *
     * @return array{success: bool, comment_id?: string, error?: string}
     */
    public function replyToInstagramComment(string $commentId, string $message): array
    {
        if (! $this->isConfigured()) {
            return ['success' => false, 'error' => 'META_PAGE_ACCESS_TOKEN is missing.'];
        }

        try {
            $response = Http::asForm()->post("{$this->graphUrl}/{$commentId}/replies", [
                'message' => $message,
                'access_token' => $this->pageAccessToken,
            ]);

            if ($response->successful()) {
                return ['success' => true, 'comment_id' => $response->json('id')];
            }

            return ['success' => false, 'error' => $this->extractErrorMessage($response)];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Resolve Instagram Account ID either from explicit config or via Facebook Page relation.
     */
    protected function resolveInstagramAccountId(): ?string
    {
        if (! empty($this->instagramAccountId)) {
            return $this->instagramAccountId;
        }

        $check = $this->testInstagramConnection();
        if ($check['success'] && ! empty($check['instagram']['id'])) {
            return (string) $check['instagram']['id'];
        }

        return null;
    }

    /**
     * Extract human-readable error from Meta Graph API response.
     */
    protected function extractErrorMessage(Response $response): string
    {
        $json = $response->json();
        if (isset($json['error']['message'])) {
            $msg = $json['error']['message'];
            if (isset($json['error']['type'])) {
                $msg .= " (Type: {$json['error']['type']})";
            }
            if (isset($json['error']['code'])) {
                $msg .= " [Code: {$json['error']['code']}]";
            }

            return $msg;
        }

        return $response->body() ?: "HTTP {$response->status()}";
    }
}
