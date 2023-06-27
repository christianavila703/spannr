<div>
    @if ($organization)
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
                    <form wire:submit.prevent="submit" class="form-errors-danger">
                        <div class="row">
                            <div class="col-12 mt-3">
                                <p class="h6">Hiring Manager Details <small class="text-muted">(where we'll send applications)</small></p>
                            </div>
                            <div class="col-12 col-md-6 mb-4">
                                <input type="text" wire:model.lazy="first_name" class="form-control" placeholder="First name" aria-label="First Name"
                                       value="{{ $user ? $user->first_name : '' }}">
                                @error('first_name') <span class="form-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-12 col-md-6 mb-4">
                                <input type="text" wire:model.lazy="last_name" class="form-control" placeholder="Last name" aria-label="Last Name"
                                       value="{{ $user ? $user->last_name : '' }}">
                                @error('last_name') <span class="form-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-12 col-md-6 mb-4">
                                <input type="text" wire:model.lazy="email" class="form-control" placeholder="Email" aria-label="Email"
                                       value="{{ $user ? $user->email : '' }}">
                                @error('email') <span class="form-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-12 col-md-6 mb-4">
                                <input type="text" wire:model.lazy="phone" class="form-control" placeholder="Phone" aria-label="Phone"
                                       value="{{ $user && $user->person ? $user->person->phone : '' }}">
                                @error('phone') <span class="form-error">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-12 pb-4">
                                <hr class="dark mt-4">
                            </div>
                            <div class="col-12 col-md-6 mb-4">
                                <label for="organization" class="h6">Organization</label>
                                <p class="lead mb-0">{{ $organization->name }}</p>
                            </div>
                            <div class="col-12 col-md-6 mb-4">
                                <label for="organization" class="h6">Job Title</label>
                                <input type="text" wire:model.lazy="name" class="form-control" >
                                @error('name') <span class="form-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-12 col-md-6 mb-4">
                                <div>
                                    <label for="category" class="h6">Job Category</label>
                                    <select wire:model="category" class="form-control" id="category">
                                        <option value="placeholder" selected disabled>Select Category</option>
                                        @foreach($categoryOptions as $option)
                                            <option value="{{ $option->id }}">{{ $option->name }}</option>
                                        @endforeach
                                        @if($categoryOtherId)
                                            <option value="{{ $categoryOtherId }}">{{ \App\Models\JobCategory::CATEGORY_OTHER }}</option>
                                        @endif
                                    </select>
                                    @error('category') <span class="form-error">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    @if ($category == $categoryOtherId)
                                        <input wire:model="category_other" type="text" class="form-control mt-2" placeholder="Enter your category" aria-label="Enter your category">
                                        @error('category_other') <span class="form-error">{{ $message }}</span> @enderror
                                    @endif
                                </div>
                            </div>
                            <div class="col-12 col-md-6 mb-4">
                                <label for="salary_min" class="h6">Salary Range</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input wire:model="salary_min" type="number" step="1" class="form-control" aria-label="Salary Minimum">
                                    <span class="input-group-text">to</span>
                                    <input wire:model="salary_max" type="number" step="1" class="form-control" aria-label="Salary Maximum (optional)">
                                </div>
                                @error('salary_min') <span class="form-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-12 col-md-6 mb-4">
                                <p class="h6">Job Type</p>
                                @foreach ($typeOptions as $option)
                                    <div class="form-check form-check-inline">
                                        <input wire:model="type" class="form-check-input" type="radio" id="type-{{ $option }}" value="{{ $option }}">
                                        <label class="form-check-label" for="type-{{ $option }}">{{ $option }}</label>
                                    </div>
                                @endforeach
                                @error('type') <div class="form-error m-0">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-6 mb-4">
                                <p class="h6">Allows remote work?</p>
                                <div class="form-check form-check-inline">
                                    <input wire:model="remote" class="form-check-input" type="radio" id="remote-yes" value="1">
                                    <label class="form-check-label" for="remote-yes">
                                        Yes
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input wire:model="remote" class="form-check-input" type="radio" id="remote-no" value="0">
                                    <label class="form-check-label" for="remote-no">
                                        No
                                    </label>
                                </div>
                                @error('remote') <div class="form-error m-0">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 mb-4">
                                <p class="h6">Location</p>
                                <div>
                                    @error('locations') <div class="form-error m-0">{{ $message }}</div> @enderror
                                    <div class="d-flex">
                                        <div>
                                            @forelse ($locations as $location)
                                                <span>
                                                    {{ $location['name'] }}
                                                </span>
                                                @if(!$loop->last) <span class="mx-1 text-muted">/</span> @endif
                                            @empty
                                                <p class="text-muted mb-0">Add one or more locations where this job is located. If fully remote, enter the headquarters / reporting office.</p>
                                            @endforelse
                                        </div>
                                        <div class="ms-auto">
                                            @if($locations)
                                                <a wire:click="clearLocations" class="btn btn-sm">clear locations</a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div wire:ignore class="mt-2">
                                    <div id="autocomplete"></div>
                                </div>
                            </div>
                            <div class="col-12 mb-4">
                                <label for="description" class="h6">
                                    <span>Job Description</span>
                                    @error('description') <span class="form-error font-sans-serif fw-normal m-0">{{ $message }}</span> @enderror
                                </label>
                                <div class="pseudo-textarea">
                                    <textarea wire:model="description" id="description"></textarea>
                                </div>
                                <div wire:ignore>
                                    <div class="tiny-editor">
                                        <textarea id="job-description" name="job-description" aria-label="Content Editor"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mb-4">
                                <label for="qualifications" class="h6">
                                    <span>Minimum Qualifications</span>
                                    @error('qualifications') <span class="form-error font-sans-serif fw-normal m-0">{{ $message }}</span> @enderror
                                </label>
                                <div wire:ignore>
                                    <div class="pseudo-textarea">
                                        <textarea wire:model.lazy="qualifications" id="qualifications" wire:ignore></textarea>
                                    </div>
                                    <div class="tiny-editor">
                                        <textarea id="job-qualifications" name="job-qualifications" aria-label="Content Editor">{{ old('qualifications') }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mb-4">
                                <p class="h6 m-0">
                                    <span>How do you want to list your job?</span>
                                    @error('monthly') <span class="form-error font-sans-serif fw-normal m-0">Please choose an option</span> @enderror
                                </p>
                                <div class="d-lg-flex">
                                    <div class="mt-3">
                                        <div class="form-check lead form-check-inline">
                                            <input wire:model="monthly" class="form-check-input" type="checkbox" id="monthly" value="1">
                                            <label class="form-check-label" for="monthly">$99 / month Job Posting</label>
                                        </div>
                                    </div>
                                    <div class="mt-3 lead">
                                        <div class="form-check lead form-check-inline">
                                            <input wire:model="recruiter" class="form-check-input" type="checkbox" id="recruiter" value="1">
                                            <label class="form-check-label" for="recruiter">Spannr Recruitment Team</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-lg btn-primary">Submit Job Posting</button>
                            </div>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    @endif
</div>
