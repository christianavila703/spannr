{{--
    DEV-7: quiet author / reviewer lines under the headline.
    The author name is author_byline, the same value the Article JSON-LD uses.
    Reviewer lines appear only when an editor filled in those fields, so the page never implies a review that did not happen.
--}}
@php
    $boxAuthor = $article->authors->first();
    $boxAuthorUrl = $boxAuthor && filled($boxAuthor->slug) ? $boxAuthor->about_page_url : null;
    $boxAuthorTitle = $boxAuthor ? trim((string) $boxAuthor->title) : '';
    $boxReviewerName = trim((string) $article->reviewer_name);
    $boxReviewerTitle = trim((string) $article->reviewer_title);
@endphp
@if($boxAuthor || $boxReviewerName !== '' || $article->reviewed_at)
    <div class="article-byline-box small text-muted mb-3">
        @if($boxAuthor)
            <div class="article-byline-author">
                By @if($boxAuthorUrl)<a href="{{ $boxAuthorUrl }}" class="text-reset fw-bold">{{ $article->author_byline }}</a>@else<span class="fw-bold">{{ $article->author_byline }}</span>@endif{{ $boxAuthorTitle !== '' ? ', ' . $boxAuthorTitle : '' }}
            </div>
        @endif
        @if($boxReviewerName !== '')
            <div class="article-byline-reviewer">
                Reviewed by <span class="fw-bold">{{ $boxReviewerName }}</span>@if($boxReviewerTitle !== ''), {{ $boxReviewerTitle }}@endif
            </div>
        @endif
        @if($article->reviewed_at)
            <div class="article-byline-reviewed-at">Last reviewed: {{ $article->reviewed_at->format('F j, Y') }}</div>
        @endif
    </div>
@endif
