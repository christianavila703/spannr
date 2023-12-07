<div class="has-collapse-arrows color-changing-border mb-4">
    <button class="btn btn-lg" type="button" data-bs-toggle="collapse"
            data-bs-target="#collapse-certifications" aria-expanded="true" aria-controls="collapse-certifications">
        <span class="icon"></span><span>Credentials (Education / Licenses / Certifications)</span>
    </button>
    <div class="collapse show" id="collapse-certifications">
        <livewire:public.marketplace.manage-listing.certifications :listing="$listing" />
    </div>
</div>
