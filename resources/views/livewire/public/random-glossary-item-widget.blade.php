<div>
    <div class="mb-1 text-center">
        <button wire:click="randomize" class="btn btn-sm btn-link text-dark">
            <i class="fa-sharp fa-solid fa-shuffle me-1"></i>
            <span>Random Glossary Term</span>
        </button>
    </div>
    <div class="sidebar-glossary-widget">
        <a href="{{ $glossary->url }}">
            <p class="h5">{{ $glossary->name }}</p>
            <p class="text mb-0">{{ $glossary->short_excerpt }}</p>
        </a>
    </div>
</div>
