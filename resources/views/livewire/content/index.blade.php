<div>
    <table class="table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Slug</th>
                <th>Updated</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($content as $contentItem)
                <tr class="align-middle">
                    <td>{{ $contentItem->name }}</td>
                    <td>
                        @if($contentItem->slug === 'home')
                            <a href="/" target="_blank" rel="noopener noreferrer">{{ $contentItem->slug }}</a>
                        @elseif($contentItem->slug === 'index-about')
                            <a href="{{ route('public.content.longevity-index.about') }}" target="_blank" rel="noopener noreferrer">{{ $contentItem->slug }}</a>
                        @elseif($contentItem->slug === 'index-terms')
                            <a href="{{ route('public.content.longevity-index.terms') }}" target="_blank" rel="noopener noreferrer">{{ $contentItem->slug }}</a>
                        @else
                            <a href="/{{ $contentItem->slug }}" target="_blank" rel="noopener noreferrer">{{ $contentItem->slug }}</a>
                        @endif
                        <span>
                            @if (in_array($contentItem->slug, $pageSlugs))
                                <span class="badge bg-primary">attached</span>
                            @endif
                        </span>
                    </td>
                    <td>{{ $contentItem->updated_at }}</td>
                    <td class="flex-grow-1 text-end">
                        <div class="d-flex align-items-center justify-content-end">
                            <a class="btn-link text-quaternary me-4" data-bs-toggle="modal" data-bs-target="#{{ $contentItem->slug }}">
                                View SEO
                            </a>
                            <form method="POST" action="{{ route('admin.content.destroy', $contentItem->id ) }}">
                                @method('DELETE')
                                @csrf
                                <button type="submit" class="btn btn-link text-danger">
                                    <i class="fa-duotone fa-trash-can me-1"></i>Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <div class="modal fade" id="{{ $contentItem->slug }}" tabindex="-1" aria-labelledby="{{ $contentItem->slug }}Label" aria-hidden="true">
                    <div class="modal-dialog modal-fullscreen">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div>
                                    <pre style="white-space: break-spaces">{{ $contentItem->seo }}</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <tr>
                    <td>No content matched your query</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="max-width-600 mt-5">
        <h2 class="h5">Attachable Pages</h2>
        @foreach ($pageSlugs as $page)
            <div class="small">
                {{ $page }}
            </div>
        @endforeach
    </div>
</div>
