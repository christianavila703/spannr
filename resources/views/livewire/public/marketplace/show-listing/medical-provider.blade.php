<div wire:poll.visible wire:init="loadMap">
    @if($listing->status === \App\Models\Listing::STATUS_PENDING)
        <div class="bg-warning h6 px-3 py-2 mb-4 d-flex align-items-center">
            <p class="me-3 mb-0">This listing is pending and not public on Spannr. Once approved, it will be in the Longevity Marketplace.</p>
            @can('edit records')
                <button wire:click="approveListing" class="btn btn-dark">Approve</button>
            @else
                <button wire:click="requestApproval" class="btn btn-dark">Request Approval</button>
            @endif
        </div>
    @endif
    @if($success)
        <div class="text-success h6 mb-4">{{ $success }}</div>
    @endif
    <div>
        @include('public.marketplace.listing-data.medical-provider')
    </div>
</div>
