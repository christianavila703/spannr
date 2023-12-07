<div class="has-collapse-arrows color-changing-border mb-4">
    <button class="btn btn-lg" type="button" data-bs-toggle="collapse"
            data-bs-target="#collapse-treatments" aria-expanded="true" aria-controls="collapse-treatments">
        <span class="icon"></span><span>Treatments / Services</span>
    </button>
    <div class="collapse show" id="collapse-treatments">
        <livewire:public.marketplace.manage-listing.medical-treatments :listing="$listing" />
    </div>
</div>
