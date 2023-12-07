<div class="has-collapse-arrows color-changing-border mb-4">
    <button class="btn btn-lg" type="button" data-bs-toggle="collapse"
            data-bs-target="#collapse-languages" aria-expanded="true" aria-controls="collapse-languages">
        <span class="icon"></span><span>Languages Spoken</span>
    </button>
    <div class="collapse show" id="collapse-languages">
        <livewire:public.marketplace.manage-listing.languages :listing="$listing" />
    </div>
</div>
