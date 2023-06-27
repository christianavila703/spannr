<?php

namespace App\View\Components\Content;

use App\Models\Content;
use Illuminate\View\Component;

class ContentMeta extends Component
{
    public Content $content;
    public bool $showReadingTime;

    public function __construct(Content $content, bool $showReadingTime = false)
    {
        $this->content = $content;
        $this->showReadingTime = $showReadingTime;
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Support\Htmlable|\Closure|string|\Illuminate\Contracts\Foundation\Application
    {
        return view('components.content.content-meta');
    }
}
