<div class="has-collapse-arrows color-changing-border mb-4">
    <button class="btn btn-lg" type="button" data-bs-toggle="collapse"
            data-bs-target="#collapse-locations" aria-expanded="true" aria-controls="collapse-locations">
        <span class="icon"></span><span>Locations</span>
    </button>
    <div class="collapse show" id="collapse-locations">
        <livewire:public.marketplace.manage-listing.addresses :listing="$listing" />
    </div>
</div>
