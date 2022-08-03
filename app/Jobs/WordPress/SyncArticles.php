<?php

namespace App\Jobs\WordPress;

use App\Models\Article;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
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
            $attributes = [
                'date' => Carbon::parse($wpPost['date'])->format('Y-m-d H:i:s'),
                'name' => html_entity_decode($wpPost['title']['rendered']),
                'slug' => $wpPost['slug'],
                'status' => $wpPost['status'],
                'content' => html_entity_decode($wpPost['content']['rendered']),
                'sticky' => $wpPost['sticky'],
                'seo' => $wpPost['yoast_head']
            ];

            if ($wpPost['excerpt']['rendered'] != '') {
                $excerpt = strip_tags(html_entity_decode($wpPost['excerpt']['rendered']));
                $excerpt = str_replace('[…]', '...', $excerpt);
                $attributes['excerpt'] = $excerpt;
            } else {
                $excerpt = strip_tags(html_entity_decode($wpPost['content']['rendered']));
                $attributes['excerpt'] = Str::words($excerpt, 40);
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
        }
    }
}
