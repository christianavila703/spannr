<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (\App\Models\Glossary::all() as $glossaryItem) {
            $doc = new DOMDocument();
            @$doc->loadHTML($glossaryItem->seo);

            $nodes = $doc->getElementsByTagName('title');

            if (isset($nodes->item(0)->nodeValue)) {
                $glossaryItem->page_title = $nodes->item(0)->nodeValue;
            }

            $metas = $doc->getElementsByTagName('meta');

            for ($i = 0; $i < $metas->length; $i++) {
                $meta = $metas->item($i);

                if($meta->getAttribute('property') == 'og:description') {
                    $content = $meta->getAttribute('content');

                    if (strlen($content) > 255) {
                        $content = substr($content, 0, 252) . '...';
                    }

                    $glossaryItem->meta_description = $content;
                }
            }

            $glossaryItem->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
