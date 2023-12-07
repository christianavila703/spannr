<div class="has-collapse-arrows color-changing-border mb-4">
    <button class="btn btn-lg" type="button" data-bs-toggle="collapse"
            data-bs-target="#collapse-user" aria-expanded="true" aria-controls="collapse-user">
        <span class="icon"></span><span>Manage Users</span>
    </button>
    <div class="collapse show" id="collapse-user">
        <p class="mb-2">Spannr users who can manage this listing</p>
        <livewire:public.marketplace.manage-listing.users :listing="$listing" />
    </div>
</div>
