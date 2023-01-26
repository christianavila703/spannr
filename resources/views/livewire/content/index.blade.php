<div>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Slug / Link</th>
                <th>Updated</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($content as $contentItem)
                <tr class="align-middle">
                    <td>
                        <a href="https://wp.spannr.com/wp-admin/post.php?post={{ $contentItem->id }}&action=edit">{{ $contentItem->id }}</a>
                    </td>
                    <td>{{ $contentItem->name }}</td>
                    <td>
                        <a href="{{ $contentItem->link }}">{{ $contentItem->slug }}</a>
                        <span>
                            @if (in_array($contentItem->slug, $pageSlugs))
                                <span class="badge bg-primary">attached</span>
                            @endif
                        </span>
                    </td>
                    <td>{{ $contentItem->updated_at }}</td>
                    <td class="flex-grow-1 text-end">
                        <div class="d-flex align-items-center justify-content-end">
                            <button class="btn btn-link text-quaternary me-4" data-bs-toggle="modal" data-bs-target="#{{ $contentItem->slug }}">
                                View SEO
                            </button>
                            <a class="btn-link text-primary me-4" href="{{ route('admin.content.edit', $contentItem->id) }}">
                                <i class="fa-duotone fa-pen-to-square me-1"></i>Edit
                            </a>
                            <form method="POST" action="{{ route('admin.content.destroy', $contentItem->id ) }}">
                                @method('DELETE')
                                @csrf
                                <button type="submit" class="btn btn-link text-danger confirm-button">
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
