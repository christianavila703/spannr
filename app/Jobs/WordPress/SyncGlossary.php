<?php

namespace App\Jobs\WordPress;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Glossary;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SyncGlossary implements ShouldQueue
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

        $rest_api_url = config('services.wordpress.url') . "/wp-json/wp/v2/glossary";

        $response = Http::withBasicAuth($username, $password)
            ->get($rest_api_url, [
                'per_page' => 99,
                'status' => 'publish'
            ]);

        $results = $response->json();

        foreach ($results as $wpPost) {
            $attributes = [
                'name' => html_entity_decode($wpPost['title']['rendered']),
                'slug' => $wpPost['slug'],
                'content' => html_entity_decode($wpPost['content']['rendered']),
                'group' => strtoupper(substr($wpPost['title']['rendered'], 0, 1)),
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

            $article = Glossary::updateOrCreate(
                ['id' => $wpPost['id']],
                $attributes
            );

        }
    }
}
