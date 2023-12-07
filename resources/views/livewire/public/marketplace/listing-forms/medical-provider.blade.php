<div class="my-4">
    <h1>Edit {{ $listing->name }}</h1>
    <div class="row">
        <div class="col-12 col-xl-6 pe-xl-5 mb-4 mb-xl-0">

            <div class="has-collapse-arrows mb-4">
                <button class="btn btn-lg" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapse-listing-details" aria-expanded="true" aria-controls="collapse-listing-details">
                    <span class="icon"></span><span>Listing Details</span>
                </button>
                <div class="collapse show" id="collapse-listing-details">
                    <form wire:submit.prevent="submit">
                        <div class="row">
                            <div class="col-12 col-md-6 mb-3">
                                @include('public.marketplace.form-fields.website')
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                @include('public.marketplace.form-fields.phone')
                            </div>
                        </div>
                        <div class="mb-3">
                            @include('public.marketplace.form-fields.summary')
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6 mb-4">
                                @include('public.marketplace.form-fields.insurance')
                            </div>
                            <div class="col-12 col-md-6 mb-4">
                                @include('public.marketplace.form-fields.virtual')
                            </div>
                        </div>
                        <div wire:init="loadListingEditor" class="mb-3">
                            @include('public.marketplace.form-fields.description')
                        </div>
                        <div class="my-3">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="has-collapse-arrows color-changing-border mb-4">
                <button class="btn btn-lg" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapse-listing-emails" aria-expanded="true" aria-controls="collapse-listing-emails">
                    <span class="icon"></span><span>Listing Emails</span>
                </button>
                <div class="collapse show" id="collapse-listing-emails">
                    <livewire:public.marketplace.manage-listing.emails :listing="$listing" />
                </div>
            </div>

            <div class="has-collapse-arrows color-changing-border mb-4">
                <button class="btn btn-lg" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapse-listing-directory" aria-expanded="true" aria-controls="collapse-listing-directory">
                    <span class="icon"></span><span>Spannr Marketplace</span>
                </button>
                <div class="collapse show" id="collapse-listing-directory">
                    <livewire:public.marketplace.manage-listing.connected-listings :listing="$listing" />
                </div>
            </div>

            @if($listing->type === \App\Models\Listing::TYPE_CLINIC OR $listing->type === \App\Models\Listing::TYPE_RETREAT)
                <div class="has-collapse-arrows color-changing-border mb-4">
                    <button class="btn btn-lg" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapse-clinic-organization" aria-expanded="true" aria-controls="collapse-clinic-organization">
                        <span class="icon"></span><span>Spannr Companies Directory</span>
                    </button>
                    <div class="collapse show" id="collapse-clinic-organization">
                        <p class="lead text-muted mb-2">Connect your marketplace listing to our companies directory</p>
                        <livewire:public.marketplace.manage-listing.relatable-organization :listing="$listing" />
                    </div>
                </div>
            @endif

{{--            <div class="has-collapse-arrows mb-4">--}}
{{--                <button class="btn btn-lg" type="button" data-bs-toggle="collapse"--}}
{{--                        data-bs-target="#collapse-people-directory" aria-expanded="true" aria-controls="collapse-people-directory">--}}
{{--                    <span class="icon"></span><span>Spannr People Directory</span>--}}
{{--                </button>--}}
{{--                <div class="collapse show" id="collapse-people-directory">--}}
{{--                    <livewire:public.marketplace.manage-listing.connected-people :listing="$listing" />--}}
{{--                </div>--}}
{{--            </div>--}}

            <div class="has-collapse-arrows color-changing-border mb-4">
                <button class="btn btn-lg" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapse-listing-images" aria-expanded="true" aria-controls="collapse-listing-images">
                    <span class="icon"></span><span>Listing Images</span>
                </button>
                <div class="collapse show" id="collapse-listing-images">
                    <div class="d-md-flex">
                        <div class="flex-grow-1 me-4">
                            <livewire:admin.has-media.upload-featured-image :model="$listing" headingClass="h6" imageRequirements="Recommended dimensions 1200x900; Max size 1024 KB" />
                        </div>
                        <div class="max-width-200 flex-shrink-1">
                            <livewire:admin.has-media.featured-image :model="$listing" />
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="col-12 col-xl-6">
            @include('public.marketplace.form-fields.certifications')
            @include('public.marketplace.form-fields.treatments')
            @include('public.marketplace.form-fields.age-groups')
            @include('public.marketplace.form-fields.languages')
            @include('public.marketplace.form-fields.addresses')
            @if ($canEditUsers)
                @include('public.marketplace.form-fields.users')
            @endif
        </div>
    </div>
</div>
