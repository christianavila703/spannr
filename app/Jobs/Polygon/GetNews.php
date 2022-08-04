<?php

namespace App\Jobs\Polygon;

use App\Models\MediaItem;
use App\Models\MediaPublisher;
use App\Models\Organization;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class GetNews implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Organization $organization;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Organization $organization)
    {
        $this->organization = $organization;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $url = 'https://api.polygon.io/v2/reference/news?ticker=' . $this->organization->stock->ticker . '&limit=10&apiKey=' . config('services.polygon.key');

        $news = Http::get($url);
        $newsArray = json_decode($news->body());

        foreach ($newsArray->results as $result) {

            $publisherAttributes = [
                'name' => $result->publisher->name,
                'url' => $result->publisher->homepage_url,
                'image_url' => $result->publisher->logo_url,
            ];

            if (property_exists($result->publisher, 'favicon_url')) {
                $publisherAttributes['favicon_url'] = $result->publisher->favicon_url;
            }

            $publisher = MediaPublisher::updateOrCreate([
                'name' => $result->publisher->name
            ], $publisherAttributes);

            $mediaItemAttributes = [
                'name' => $result->title,
                'publisher_id' => $publisher->id,
                'external_id' => $result->id,
                'date' => Carbon::parse($result->published_utc)->format('Y-m-d H:i:s'),
                'url' => $result->article_url,
                'summary' => $result->description,
                'data' => json_encode($result)
            ];

            if (property_exists($result, 'image_url')) {
                $mediaItemAttributes['image_url'] = $result->image_url;
            }

            if (property_exists($result, 'description')) {
                $mediaItemAttributes['summary'] = $result->description;
            }

            $mediaItem = MediaItem::updateOrCreate([
                'external_id' => $result->id
            ], $mediaItemAttributes);

            $mediaItem->organizations()->syncWithoutDetaching([$this->organization->id]);
        }
    }
}
