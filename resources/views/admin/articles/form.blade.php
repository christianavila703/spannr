<form method="POST" action="{{ $route }}" enctype="multipart/form-data">
    @csrf
    @isset($method)
        @method($method)
    @endisset
    <div class="row">
        <div class="col-12">
            <x-forms.input type="text" name="name" label="Title" class="mb-3"
                           value="{{ $article->name ?? old('name') }}"/>
        </div>
        <div class="col-12">
            <div class="d-flex flex-wrap">
                <x-forms.input type="datetime-local" name="date" label="Date" class="mb-3 me-3"
                               value="{{ $article->date ?? old('date') }}"/>
                <x-forms.select name="status" label="Status" class="mb-3 me-3">
                    @foreach (\App\Models\Article::STATUSES as $status)
                        <option value="{{ $status }}"
                                @isset($article)
                                    @if($article->status == $status) selected @endif
                                @else
                                    @if(old('status') == $status) selected @endif
                            @endisset
                        >{{ $status }}</option>
                    @endforeach
                </x-forms.select>
                <x-forms.input type="text" name="slug" label="Slug" class="mb-3 flex-grow-1"
                               value="{{ $article->slug ?? old('slug') }}"/>
            </div>
        </div>
        <div class="col-12">
            <x-forms.input type="text" name="page_title" label="Page Title" class="mb-3"
                           value="{{ $article->page_title ?? old('page_title') }}"/>
        </div>
        <div class="col-12">
            <x-forms.input type="text" name="seo_title" label="SEO title (Google)"
                           class="mb-3"
                           value="{{ old('seo_title', isset($article) ? (optional($article->seo)->title ?? '') : '') }}"/>
            <p class="small text-muted mb-3">Optional. Used for the HTML &lt;title&gt; and search snippets when set. Falls back to Page Title, then article title. Saved on the SEO record, not as the on-page headline.</p>
        </div>
        <div class="col-12 col-md-6 mb-3 mb-md-0">
            <div class="form-floating">
                <textarea class="form-control" placeholder="Excerpt" name="excerpt" id="excerpt"
                          style="height: 180px">{{ $article->excerpt ?? old('excerpt') }}</textarea>
                <label for="excerpt">Excerpt</label>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="form-floating">
                <textarea class="form-control" placeholder="Meta Description" name="meta_description" id="meta_description"
                          style="height: 180px">{{ $article->meta_description ?? old('meta_description') }}</textarea>
                <label for="meta_description">Meta Description</label>
                <div class="small">
                    <span id="meta_description_character_count" class="badge bg-gray-medium me-2">0</span>
                    <strong>characters</strong>
                </div>
                <p class="small text-muted mt-1 mb-0">Synced to the SEO record and used for the meta description tag (with fallbacks to excerpt if empty).</p>
            </div>
        </div>
        @php
            $faqRows = old('faq');
            if ($faqRows === null) {
                $faqRows = isset($article) && is_array($article->faq) && count($article->faq) ? $article->faq : [['question' => '', 'answer' => '']];
            }
            if (! is_array($faqRows) || $faqRows === []) {
                $faqRows = [['question' => '', 'answer' => '']];
            }
        @endphp
        <div class="col-12 mb-4">
            <p class="h5">FAQ (structured data)</p>
            <p class="small text-muted">Optional Q&amp;A pairs output as FAQPage JSON-LD when the post is published.</p>
            <div id="faq-repeater">
                @foreach ($faqRows as $i => $pair)
                    <div class="faq-row border rounded p-3 mb-3 bg-light">
                        <x-forms.input type="text" name="faq[{{ $i }}][question]" label="Question" class="mb-2"
                                       value="{{ $pair['question'] ?? '' }}"/>
                        <div class="form-floating">
                            <textarea class="form-control" name="faq[{{ $i }}][answer]" placeholder="Answer"
                                      style="height: 100px">{{ $pair['answer'] ?? '' }}</textarea>
                            <label>Answer</label>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger mt-2 js-remove-faq" @if(count($faqRows) < 2) style="display:none" @endif>Remove</button>
                    </div>
                @endforeach
            </div>
            <button type="button" class="btn btn-sm btn-secondary" id="faq-add">Add FAQ</button>
        </div>
        <div class="col-12 col-md-6 mb-3 mb-md-0">
            <label for="authors" class="visually-hidden">Select Authors</label>
            <select multiple name="authors[]" id="authors" class="js-choice-authors" data-url="{{ $authorsUrl }}">
                <option value="" disabled>Select Authors</option>
            </select>
        </div>
        <div class="col-12 col-md-6">
            <label for="tags" class="visually-hidden">Select Tags</label>
            <select multiple name="tags[]" id="tags" class="js-choice-tags" data-url="{{ $tagsUrl }}">
                <option value="">Select Tags</option>
            </select>
        </div>
        <div class="col-12 mt-4">
            <p class="h5 mb-1">Review (optional)</p>
            <p class="small text-muted">Leave all three empty unless a named person has actually reviewed this article. Anything entered here is shown publicly under the headline.</p>
            <div class="row">
                <div class="col-12 col-md-4 mb-3">
                    <x-forms.input type="text" name="reviewer_name" label="Reviewed by (name)"
                                   value="{{ old('reviewer_name', isset($article) ? $article->reviewer_name : '') }}"/>
                </div>
                <div class="col-12 col-md-4 mb-3">
                    <x-forms.input type="text" name="reviewer_title" label="Reviewer title"
                                   value="{{ old('reviewer_title', isset($article) ? $article->reviewer_title : '') }}"/>
                </div>
                <div class="col-12 col-md-4 mb-3">
                    <x-forms.input type="date" name="reviewed_at" label="Last reviewed"
                                   value="{{ old('reviewed_at', isset($article) ? optional($article->reviewed_at)->format('Y-m-d') : '') }}"/>
                </div>
            </div>
        </div>
        <div class="col-12 my-3">
            <p class="h4">Content</p>
            <x-tiny-mce.tiny-editor id="content" name="content" value="{{ $article->content ?? old('content') }}"/>
        </div>
        <div class="col-12 my-3">
            <p class="h4">Sources</p>
            <x-tiny-mce.tiny-editor id="sources" name="sources" value="{{ $article->sources ?? old('sources') }}"/>
        </div>
        <div class="col-12 my-3">
            <p class="h4">Sidebar</p>
            <x-tiny-mce.tiny-editor id="sidebar" name="sidebar" value="{{ $article->sidebar ?? old('sidebar') }}"/>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary h5">Save Post</button>
        </div>
    </div>
</form>

@push('end')
    <x-tiny-mce.tiny-config/>

    <script>
        const authorChoice = new Choices('#authors', {
            removeItemButton: true,
        });
        const authorsUrl = document.getElementById('authors').getAttribute('data-url');

        authorChoice.setChoices(async () => {
            try {
                const items = await fetch(authorsUrl);
                return items.json();
            } catch (err) {
                console.error(err);
            }
        });

        const tagChoice = new Choices('#tags', {
            removeItemButton: true,
        });
        const tagsUrl = document.getElementById('tags').getAttribute('data-url');

        tagChoice.setChoices(async () => {
            try {
                const items = await fetch(tagsUrl);
                return items.json();
            } catch (err) {
                console.error(err);
            }
        });

    </script>

    @vite('resources/js/meta-descriptions.js')

    <script>
        (function () {
            const container = document.getElementById('faq-repeater');
            const addBtn = document.getElementById('faq-add');
            if (!container || !addBtn) return;

            function nextIndex() {
                return container.querySelectorAll('.faq-row').length;
            }

            function updateRemoveButtons() {
                const rows = container.querySelectorAll('.faq-row');
                rows.forEach(function (row) {
                    const btn = row.querySelector('.js-remove-faq');
                    if (btn) btn.style.display = rows.length > 1 ? '' : 'none';
                });
            }

            addBtn.addEventListener('click', function () {
                const i = nextIndex();
                const wrap = document.createElement('div');
                wrap.className = 'faq-row border rounded p-3 mb-3 bg-light';
                wrap.innerHTML =
                    '<div class="mb-2"><label class="form-label">Question</label>' +
                    '<input type="text" class="form-control" name="faq[' + i + '][question]" value=""></div>' +
                    '<div class="form-floating"><textarea class="form-control" name="faq[' + i + '][answer]" placeholder="Answer" style="height:100px"></textarea><label>Answer</label></div>' +
                    '<button type="button" class="btn btn-sm btn-outline-danger mt-2 js-remove-faq">Remove</button>';
                container.appendChild(wrap);
                wrap.querySelector('.js-remove-faq').addEventListener('click', function () {
                    wrap.remove();
                    updateRemoveButtons();
                });
                updateRemoveButtons();
            });

            container.querySelectorAll('.js-remove-faq').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    btn.closest('.faq-row').remove();
                    updateRemoveButtons();
                });
            });
        })();
    </script>
@endpush
