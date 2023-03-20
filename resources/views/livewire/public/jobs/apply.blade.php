<div>
    <div>
        @if ($error)
            <div class="alert alert-danger">
                {{ $error }}
            </div>
        @endif
        @if ($success)
            <div class="text-tertiary h5">
                {{ $success }}
            </div>
        @endif
    </div>
    <div>
        @if (!$success)
            <form wire:submit.prevent="submit" class="form-errors-danger max-width-800">
                <div class="row">
                    <div class="col-12">
                        <p class="h6">Your Details</p>
                    </div>
                    <div class="col-12 col-md-6 mb-4">
                        <input type="text" wire:model.lazy="first_name" class="form-control" placeholder="First name" aria-label="First Name">
                        @error('first_name') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-12 col-md-6 mb-4">
                        <input type="text" wire:model.lazy="last_name" class="form-control" placeholder="Last name" aria-label="Last Name">
                        @error('last_name') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-12 col-md-6 mb-4">
                        <input type="text" wire:model.lazy="email" class="form-control" placeholder="Email" aria-label="Email">
                        @error('email') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-12 col-md-6 mb-4">
                        <input type="text" wire:model.lazy="phone" class="form-control" placeholder="Phone" aria-label="Phone">
                        @error('phone') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-12 mb-4">
                        <p class="h6">Location</p>
                        <div wire:ignore>
                            <div id="autocomplete"></div>
                        </div>
                        <div>
                            @error('locationName') <div class="form-error m-0">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-12 mb-4">
                        <div class="d-md-flex">
                            <label for="linkedin" class="h6 me-4 my-2 flex-shrink-0 min-width-80">LinkedIn</label>
                            <div class="input-group flex-grow-1">
                                <span class="input-group-text">https://www.linkedin.com/in/</span>
                                <input type="text" wire:model.lazy="linkedin" id="linkedin" class="form-control flex-grow-1" placeholder="sherlockholmes">
                                @error('linkedin') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mb-4">
                        <div class="d-md-flex">
                            <label for="resume" class="h6 me-4 my-2 flex-shrink-0 min-width-80">Resume</label>
                            <div>
                                <input wire:model="resume" class="form-control" type="file" id="resume">
                                @error('resume') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mb-4">
                        <textarea wire:model.lazy="message" class="form-control" rows="4" placeholder="Anything else you'd like to include?"></textarea>
                    </div>
                    <div class="col-12 mb-4">
                        <p class="h6">I give Spannr permission to reach out to me in order to provide follow up information for this job and similar opportunities.</p>
                        <div>
                            <div class="form-check lead form-check-inline">
                                <input wire:model="recruiter" class="form-check-input" type="radio" id="recruiter-yes" value="1">
                                <label class="form-check-label" for="recruiter-yes">
                                    Yes
                                </label>
                            </div>
                            <div class="form-check lead form-check-inline">
                                <input wire:model="recruiter" class="form-check-input" type="radio" id="recruiter-no" value="0">
                                <label class="form-check-label" for="recruiter-no">
                                    No
                                </label>
                            </div>
                        </div>
                        @error('recruiter') <div class="form-error m-0">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-lg btn-primary">Submit Application</button>
                    </div>
                </div>
            </form>
        @endif
    </div>
</div>
