<?php

use App\Models\Content;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    const CRYOTHERAPHY = 'Cryotherapy - Spannr Longevity Directory';
    const IVTHERARPY = 'IV Therapy - Spannr Longevity Directory';

    public function up()
    {
        Content::create([
            'name' => self::CRYOTHERAPHY,
            'slug' => 'marketplace--cryotherapy',
            'status' => Content::STATUS_PUBLISHED,
            'meta_description' => 'Looking for cryotherapy near you? Use Spannr to find the best cryotherapy locations in your area.',
            'content' => '<h2>Cryotherapy Near Me</h2>
<p>Looking for <a href="https://spannr.com/articles/cryotherapy-and-longevity">cryotherapy</a> near you? Use Spannr to find the best cryotherapy locations in your area.&nbsp;</p>
<p>If you&rsquo;re looking for a studio that offers cryotherapy, we&rsquo;ve researched the studios in your area that offer this service.&nbsp;</p>
<p>If you&rsquo;ve never been to a cryotherapy studio before, scroll down to read more about what you can expect, how to find the best cryotherapy studios, and the benefits of cryotherapy studios.</p>'
        ]);

        Content::create([
            'name' => self::IVTHERARPY,
            'slug' => 'marketplace--iv-therapy',
            'status' => Content::STATUS_PUBLISHED,
            'meta_description' => 'Looking for IV therapy near you? Use Spannr to find the best IV therapy locations in your area.',
            'content' => '<h2>IV Therapy Near Me</h2>
<p>Looking for <a href="https://spannr.com/articles/iv-therapy-for-longevity">IV therapy</a> near you? Use Spannr to find the best IV therapy locations in your area.&nbsp;</p>
<p>If you&rsquo;re looking for a studio that offers IV therapy, we&rsquo;ve researched the studios in your area that offer this service.&nbsp;</p>
<p>If you&rsquo;ve never been to an IV therapy studio before, scroll down to read more about what you can expect, how to find the best IV therapy studios, and the benefits of IV therapy studios.</p>'
        ]);
    }

    public function down() {
        Content::where('name', self::CRYOTHERAPHY)->orWhere('name', self::IVTHERARPY)->delete();
    }
};
