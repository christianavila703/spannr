<div>
    <div>
        @if($error)
            <div class="fs-5 mt-5 text-danger">
                Sorry, there was a problem with claiming this listing and our team was notified. Please try again later.
            </div>
        @else
            <div>
                @if($success)
                    <div class="mt-lg-5 border border-2 border-success p-4 max-width-600">
                        <p class="h4 text-uppercase">
                            You've successfully claimed this listing!
                        </p>
                        <p class="fs-5 mb-3">
                            If you are logged in, <a href="{{ $listing->url }}">click here</a> to manage your listing.
                        </p>
                        <p class="mb-0">If this is your first time at Spannr we created an account for you. For convenience, you can <a href="{{ route('login.link') }}">login via an email link</a>, or <a href="{{ route('password.request') }}">set a password here</a>.</p>
                    </div>
                @elseif($manualClaimSuccess)
                    <div class="mt-lg-5 border border-2 border-success p-4 max-width-600">
                        <p class="h4 text-uppercase">
                            Thanks for claiming this listing!
                        </p>
                        <p class="fs-5 mb-3">
                            We weren't able to automatically verify you - we'll review and get back to you shortly.
                        </p>
                        <p class="mb-0">To expedite the verification process, email <a href="mailto:admin@spannr.com">admin@spannr.com</a> with info supporting your ownership / management of this organization.</p>
                    </div>
                @else
                    <div>
                        @if($allowClaim)
                            <div class="mt-lg-5 border border-quaternary border-2 p-4 max-width-500">
                                <p class="h4 mb-3 text-uppercase">Is this your {{ $listing->type }}?</p>
                                <p class="fs-5 mb-3">Improve your company's profile on the only marketplace dedicated to longevity seekers</p>

                                <div>
                                    @if($showForm)
                                        <div>
                                            <form wire:submit.prevent="save">
                                                <div class="row">
                                                    <div class="col-12 col-md-6 mb-3">
                                                        <input wire:model="first_name" type="text" class="form-control" id="first_name"
                                                               aria-label="First name" placeholder="First name">
                                                        @error('first_name')
                                                        <div class="text-danger font-size-sm">{{ $message }}</div> @enderror
                                                    </div>
                                                    <div class="col-12 col-md-6 mb-3">
                                                        <input wire:model="last_name" type="text" class="form-control" id="last_name"
                                                               aria-label="Last name" placeholder="Last name">
                                                        @error('last_name')
                                                        <div class="text-danger font-size-sm">{{ $message }}</div> @enderror
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <input wire:model="email" type="email" class="form-control" id="email"
                                                           aria-label="Email" placeholder="Email">
                                                    @error('email')
                                                    <div class="text-danger font-size-sm">{{ $message }}</div> @enderror
                                                </div>
                                                <div class="mt-4">
                                                    <button type="submit" class="btn btn-primary btn-lg">Claim Listing</button>
                                                </div>
                                            </form>
                                        </div>
                                    @else
                                        <div>
                                            <button wire:click="showClaimForm" type="button" class="btn btn-primary btn-lg">Claim Listing</button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
