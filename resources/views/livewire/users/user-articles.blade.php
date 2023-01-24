<div>
    <div class="article-container">
        @foreach($articles as $article)
            <x-content.article-card :article="$article" />
        @endforeach
    </div>
    <div class="mt-5">
        {{ $articles->links() }}
    </div>

    @push('end')
        <script>
            Livewire.on('gotoTop', () => {
                window.scrollTo({
                    top: 400,
                    left: 15,
                    behaviour: 'smooth'
                })
            })
        </script>
    @endpush
</div>
