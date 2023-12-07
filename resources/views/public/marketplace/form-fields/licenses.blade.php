<div class="has-collapse-arrows mb-4">
    <button class="btn btn-lg" type="button" data-bs-toggle="collapse"
            data-bs-target="#collapse-licenses" aria-expanded="true" aria-controls="collapse-licenses">
        <span class="icon"></span><span>Licenses</span>
    </button>
    <div class="collapse show" id="collapse-licenses">
        <livewire:public.marketplace.manage-listing.licenses :listing="$listing" />
    </div>
</div>
