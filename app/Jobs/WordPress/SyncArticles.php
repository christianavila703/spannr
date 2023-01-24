<?php

namespace App\Jobs\WordPress;

use App\Models\Article;
use App\Models\Tag;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SyncArticles implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $username = config('services.wordpress.username');
        $password = config('services.wordpress.password');

        $rest_api_url = config('services.wordpress.url') . "/wp-json/wp/v2/posts?_embed";

        $response = Http::withBasicAuth($username, $password)
            ->get($rest_api_url, [
                'per_page' => 99,
                'status' => 'publish'
            ]);

        $results = $response->json();

        foreach ($results as $wpPost) {
            $seo = $wpPost['yoast_head'];
            $seo = str_replace('wp.spannr.com', 'spannr.com', $seo);
            $seo = str_replace('spannr.com/wp-content/uploads', 'wp.spannr.com/wp-content/uploads', $seo);

            $content = html_entity_decode($wpPost['content']['rendered']);
            $content = str_replace('//wp.spannr.com', '//spannr.com', $content);
            $content = str_replace('//spannr.com/wp-content/uploads', '//wp.spannr.com/wp-content/uploads', $content);

            $attributes = [
                'date' => $wpPost['date'],
                'name' => html_entity_decode($wpPost['title']['rendered']),
                'slug' => $wpPost['slug'],
                'status' => $wpPost['status'],
                'content' => $content,
                'sticky' => $wpPost['sticky'],
                'seo' => $seo,
                'user_id' => $this->matchAuthorToUser($wpPost['author'])
            ];

            if ($wpPost['excerpt']['rendered'] != '') {
                $excerpt = strip_tags(html_entity_decode($wpPost['excerpt']['rendered']));
                $excerpt = str_replace('[…]', '...', $excerpt);
                $attributes['excerpt'] = $excerpt;
            } else {
                $excerpt = strip_tags(html_entity_decode($wpPost['content']['rendered']));
                $attributes['excerpt'] = Str::words($excerpt, 40);
            }

            if (array_key_exists('description', $wpPost['yoast_head_json'])) {
                $attributes['meta_description'] = $wpPost['yoast_head_json']['description'];
            }

            $article = Article::updateOrCreate(
                ['id' => $wpPost['id']],
                $attributes
            );

            if ($wpPost['featured_image_url'] != '') {

                $imageFile = file_get_contents($wpPost['featured_image_url']);
                $imageExtensionArray = explode('.', $wpPost['featured_image_url']);
                $imageExtension = end($imageExtensionArray);

                $fileName = $article->slug . '.' . $imageExtension;
                Storage::put('public/images/articles/' . $fileName, $imageFile);
                $article->image = $fileName;
                $article->save();

            }

            try {
                $categories = $wpPost['yoast_head_json']['schema']['@graph'][0]['articleSection'];
                $tags = Tag::whereIn('name', $categories)->pluck('id')->toArray();
                $article->tags()->syncWithoutDetaching($tags);

            } catch(\Throwable $exception) {
                Log::warning('Error with article categories ' . $exception->getMessage(), $wpPost);
            }
        }
    }

    private function matchAuthorToUser($authorId): ?int
    {
        return match($authorId) {
            1 => 1, // Syd
            2 => 5, // Dillon
            3 => 7, // Luke,
            4 => 4, // Cody
            5 => 11, // Jack
            default => NULL
        };
    }
}
