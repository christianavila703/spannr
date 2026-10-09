<?php

namespace Database\Factories;

use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    protected $model = Article::class;

    public function definition()
    {
        $name = $this->faker->unique()->sentence(4);

        return [
            'name' => $name,
            'slug' => \Illuminate\Support\Str::slug($name),
            'status' => Article::STATUS_PUBLISHED,
            'date' => now(),
            'excerpt' => $this->faker->sentence(),
            'content' => '<p>Body text of the article, long enough to count as a paragraph.</p>',
        ];
    }

    public function draft(): static
    {
        return $this->state(['status' => Article::STATUS_DRAFT]);
    }

    public function slug(string $slug): static
    {
        return $this->state(['slug' => $slug, 'name' => 'Guide ' . $slug]);
    }
}
