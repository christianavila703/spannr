<div class="has-collapse-arrows color-changing-border mb-4">
    <button class="btn btn-lg" type="button" data-bs-toggle="collapse"
            data-bs-target="#collapse-age-groups" aria-expanded="true" aria-controls="collapse-age-groups">
        <span class="icon"></span><span>Age Groups Served</span>
    </button>
    <div class="collapse show" id="collapse-age-groups">
        <livewire:public.marketplace.manage-listing.age-groups :listing="$listing" />
    </div>
</div>
