<div>
    @if($showSelectOrganization)
        <div class="border border-2 border-quaternary bg-gray-medium p-3 mb-4">
            <p class="h4 mb-3">We have three ways to help you find the right candidate:</p>
            <ol class="mt-2 mb-0 lead">
                <li>Simply list your job opening by picking your organization below. If your organization isn't listed, click "Add New Organization." Your listing will be added to our jobs database, and searchable by potential candidates.</li>
                <li>Subscribe to our $99/month jobs service. Your job will be featured at the top of our jobs page, in our weekly newsletter, and on our social channels. You can cancel at anytime or once the job is filled.</li>
                <li>Work with our professional recruiting firm who will search the globe for the perfect candidate. You won't pay unless we find you the perfect candidate.</li>
            </ol>
        </div>
        <div>
            <p class="lead">Let's get started by selecting the hiring company from our database or adding a new one.</p>
        </div>
        <div>
            @if ($createNew)
                <div>
                    <h2 class="h4 mb-3 me-2">Add New Organization</h2>
                    <form wire:submit.prevent="submitOrganization" class="max-width-420 form-errors-danger">
                        <div class="mb-3">
                            <label for="name" class="fw-bold">Name</label>
                            <input type="text" wire:model="name" class="form-control" id="name">
                            @error('name') <span class="form-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="website" class="fw-bold">Website</label>
                            <input type="text" wire:model="website" class="form-control" placeholder="https://" id="website">
                            @error('website') <span class="form-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3 row">
                            <strong class="d-block">Organization Type</strong>
                            @foreach ($organizationTypes as $type)
                                <div class="col-12 col-md-6">
                                    <div class="form-check">
                                        <input wire:model="type" class="form-check-input" type="radio" id="type-{{ $type }}" value="{{ $type }}">
                                        <label class="form-check-label" for="type-{{ $type }}">{{ $type }}</label>
                                    </div>
                                </div>
                            @endforeach
                            @error('type') <div class="form-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-lg btn-primary">Save &amp; Continue</button>
                        </div>
                    </form>
                    <div class="d-md-flex justify-content-end mt-3 mt-lg-0">
                        <button wire:click="showForm(false)" tabindex="0" class="btn btn-lg btn-tertiary">Select Organization</button>
                    </div>
                </div>
            @else
                <div>
                    <div>
                        <h2 class="h4 mb-3 me-2">Select Organization</h2>
                    </div>
                    <div class="mb-3 max-width-300">
                        <input wire:model="search" type="text" class="form-control" placeholder="Search" aria-label="Search" @if($selectedOrganization) disabled @endif>
                    </div>
                    @if($search)
                        <div>
                            @if (!$selectedOrganization)
                                <div class="organization-search-container border ps-3">
                                    <div class="row">
                                        @forelse($records as $organization)
                                            <div wire:key="{{ $organization->slug }}" class="col-12 col-xl-6 px-0">
                                                <div class="py-1 pe-1">
                                                    @if ($selectedOrganization)
                                                        @if ($selectedOrganization->id === $organization->id)
                                                            <button class="btn btn-link text-tertiary select-organization-button"
                                                                    aria-label="Select {{ $organization->name }}" tabindex="0">
                                                                <i class="icon fa-sharp fa-solid fa-square-check"></i>
                                                                <span>{{ $organization->name }}</span>
                                                            </button>
                                                        @else
                                                            <button wire:click="selectOrganization('{{ $organization->id }}')"
                                                                    class="btn btn-link select-organization-button" aria-label="Select {{ $organization->name }}" tabindex="0">
                                                                <i class="icon fa-sharp fa-regular fa-square"></i>
                                                                <span>{{ $organization->name }}</span>
                                                            </button>
                                                        @endif
                                                    @else
                                                        <button wire:click="selectOrganization('{{ $organization->id }}')"
                                                                class="btn btn-link select-organization-button" aria-label="Select {{ $organization->name }}" tabindex="0">
                                                            <i class="icon fa-sharp fa-regular fa-square"></i>
                                                            <span>{{ $organization->name }}</span>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        @empty
                                            <div class="col-12 py-4">
                                                <p class="lead m-0">No organizations match your query. <a wire:click="showForm(true)" class="fw-bold text-primary">Add your organization</a></p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                                <div class="mt-3">
                                    {{ $records->links() }}
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
                <div class="mt-3">
                    @if($selectedOrganization)
                        <div class="lead me-3 mb-3">
                            <span class="me-1 text-secondary">Selected Organization:</span>
                            <strong>{{ $selectedOrganization->name }}</strong>
                            <button wire:click="clearOrganization" class="btn btn-sm btn-link text-danger">
                                <i class="fa-sharp fa-regular fa-xmark fa-lg"></i><span>Clear</span>
                            </button>
                        </div>
                        <button wire:click="continueToJob" class="btn-primary btn btn-lg">Save &amp; Continue</button>
                    @endif
                </div>
                <div class="d-md-flex justify-content-end mt-3 mt-lg-0">
                    <button wire:click="showForm(true)" tabindex="0" class="btn btn-lg btn-tertiary">Add New Organization</button>
                </div>
            @endif
        </div>
    @endif
</div>
