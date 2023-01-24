<?php

namespace App\View\Components\Content;

use App\Models\Article;
use Illuminate\View\Component;

class ArticleCard extends Component
{
    public Article $article;
    public bool $shortExcerpt;

    public function __construct(Article $article, bool $shortExcerpt = false)
    {
        $this->article = $article;
        $this->shortExcerpt = $shortExcerpt;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.content.article-card');
    }
}
