<?php

use App\Models\Tag;
use Illuminate\Database\Migrations\Migration;
use Symfony\Component\Console\Output\ConsoleOutput;

return new class extends Migration
{
    const TAG_NAME = Tag::TAG_TREATMENTS;

    public function up()
    {
        $output = new ConsoleOutput();

        $children = [
            'Cold Plunge',
            'Red Light Therapy',
            'Sauna',
        ];

        $parentTag = Tag::create([
            'name' => self::TAG_NAME
        ]);

        foreach ($children as $child) {
            try {
                Tag::create([
                    'name' => $child,
                    'parent_id' => $parentTag->id,
                    'depth' => 1
                ]);
            } catch(Throwable $exception) {
                $output->writeln("\n<info>{$child} is already a tag</info>");
            }

        }
    }

    public function down()
    {
        $tag = Tag::where('name', self::TAG_NAME)->first();
        Tag::where('parent_id', $tag->id)->delete();
        $tag->delete();
    }
};
