<div>
    <div class="mb-4 d-flex align-items-center">
        <div class="me-4">
            <input wire:model="filters.search" type="text" class="form-control" placeholder="Search...">
        </div>
        <div>
            <button wire:click="resetFilters" class="btn btn-sm">Reset Filters</button>
        </div>
        <div class="ms-auto lead">
            {{ $articles->total() }} results
        </div>
    </div>
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="me-5">
            @if (!empty($selected))
                <div>
                    <strong>{{ count($selected) }}</strong> selected
                </div>
            @else
                <div>&nbsp;</div>
            @endif
        </div>
        <div wire:ignore class="dropdown">
            <button class="btn btn-outline-dark" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                Bulk Actions <i class="fa fa-angle-down ml-5" aria-hidden="true"></i>
            </button>
            <ul class="dropdown-menu">
                <li>
                    <a wire:click="setTopInsight" class="dropdown-item" href="#">
                        <i class="fa-sharp fa-solid fa-stars fa-fw me-1 text-warning"></i>Mark Top Insight
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <table class="table">
        <thead>
        <tr>
            <th>
                <div class="form-check">
                    <input wire:model="selectPage" class="form-check-input" type="checkbox" aria-label="Select all">
                </div>
            </th>
            <th class="sortable-column max-width-200">Date</th>
            <th class="text-center">
                WP Image
            </th>
            <th class="text-center">
                Images
            </th>
            <th class="sortable-column max-width-200">Status</th>
            <th class="sortable-column">Title</th>
            <th>Categories</th>
            <th>Meta Description</th>
            <th>Author</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach($articles as $article)
            <tr>
                <td>
                    <div class="form-check">
                        <input wire:model="selected" class="form-check-input" type="checkbox" value="{{ $article->id }}" aria-label="Select {{ $article->name }}">
                    </div>
                </td>
                <td class="text-nowrap">
                    {{ $article->formatted_date }}
                </td>
                <td class="text-center">
                    @if ($article->image_url)
                        <img src="{{ $article->image_url }}" alt="{{ $article->name }}" style="width: 80px">
                    @endif
                </td>
                <td class="text-center">
                    <span class="d-block">
                        @if($article->featured_image_url)
                            <img src="{{ $article->featured_image_url }}" alt="{{ $article->name }}" style="width: 80px">
                        @endif
                        <span class="d-block">
                            <a href="{{ route('admin.articles.media', $article->id) }}" class="btn btn-link btn-sm text-nowrap">View Media</a>
                        </span>
                    </span>
                </td>
                <td>{{ $article->status }}</td>
                <td>
                    <div class="max-width-500">
                        <a href="{{ $article->url }}">{{ $article->name }}</a>
                    </div>
                </td>
                <td>
                    <div class="max-width-200">
                        @foreach ($article->tags as $tag)
                            <span class="badge {{ $tag->color_badge }}">{{ $tag->name }}</span>
                        @endforeach
                    </div>
                </td>
                <td>
                    <div class="max-width-500 small">
                        {{ $article->meta_description }}
                    </div>
                    @if($article->meta_description)
                        <span class="badge bg-{{ $article->meta_description_character_count_color }}">{{ $article->meta_description_character_count }}</span>
                    @endif
                </td>
                <td>
                    @if ($article->author)
                        {{ $article->author->name }}
                    @endif
                </td>
                <td class="text-end">
                    <a href="{{ route('admin.articles.edit', $article->id) }}" class="btn btn-sm btn-primary me-2 mb-1">edit</a>

                    <button type="button" class="btn btn-sm btn-tertiary" data-bs-toggle="modal" data-bs-target="#seo-modal-{{ $article->id }}">
                        yoast
                    </button>

                    <div class="modal fade" id="seo-modal-{{ $article->id }}" tabindex="-1" aria-labelledby="seo-modal-{{ $article->id }}Label"
                         aria-hidden="true">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header border-0">
                                    <h1 class="modal-title h4" id="seo-modal-{{ $article->id }}Label">{{ $article->name }}</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-start">
                                    <pre class="wrap-words">{{ $article->seo }}</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-between my-4">
        <div class="d-flex align-items-center me-4">
            <select wire:model="perPage" id="perPage" class="form-select">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <label for="perPage" class="d-block fw-bold ms-2 flex-shrink-0">Per Page</label>
        </div>

        {{ $articles->links() }}
    </div>
</div>
