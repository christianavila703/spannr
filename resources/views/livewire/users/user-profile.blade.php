<div>
    <form wire:submit.prevent="save" enctype="multipart/form-data">
        <div class="row">
            <div class="col-12 col-lg-6">
                <div class="row">
                    <div class="col-12 col-lg-6 mb-3">
                        <label for="name">First Name</label>
                        <input type="text" wire:model="user.name" class="form-control" id="name">
                        @error('user.name') <div class="fw-bold text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12 col-lg-6 mb-3">
                        <label for="last_name">Last Name</label>
                    <input type="text" wire:model="user.last_name" class="form-control" id="last_name">
                    @error('user.last_name') <div class="fw-bold text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-lg-6 mb-3">
                        <label for="title">Title</label>
                        <input type="text" wire:model="user.title" class="form-control" id="title">
                        @error('user.title') <div class="fw-bold text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12 col-lg-6 mb-3">
                        <label for="byline">Byline <small class="text-muted">used instead of first + last</small></label>
                        <input type="text" wire:model="user.byline" class="form-control" id="byline">
                        @error('user.byline') <div class="fw-bold text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="bio">
                        <span class="me-3">Bio</span>
                        <span class="small text-muted">
                            <strong>{{ strlen($user->bio) }}</strong>
                            <span>characters</span>
                        </span>
                    </label>
                    @error('user.bio') <div class="fw-bold text-danger">{{ $message }}</div> @enderror
                    <textarea wire:ignore.self wire:model="user.bio" class="form-control" id="bio" style="height: 120px"></textarea>
                </div>
            </div>
            <div class="col-12 col-lg-6 ps-lg-5 mb-3">
                <div class="mb-5 d-lg-flex align-items-center">
                    <div wire:poll.visible class="image-bg-container-100 border flex-shrink-0" style="background-image: url('{{ $currentProfilePhoto }}');"></div>
                    <div class="ps-lg-3">
                        <label for="profilePhoto">Profile Photo</label>
                        <input type="file" id="profilePhoto" wire:model.debounce.500ms="profilePhoto" class="form-control max-width-500 me-3">
                        @error('profilePhoto') <div class="text-danger fw-bold">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="mb-4 d-lg-flex align-items-center">
                    <div wire:poll.visible class="image-bg-container-100 border flex-shrink-0" style="background-image: url('{{ $currentTeamPhoto }}');"></div>
                    <div class="ps-lg-3">
                        <label for="profilePhoto">Team Photo</label>
                        <input type="file" id="teamPhoto" wire:model.debounce.500ms="teamPhoto" class="form-control max-width-500 me-3">
                        @error('teamPhoto') <div class="text-danger fw-bold">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center">
            <button type="submit" class="btn btn-primary">Save Profile</button>
            @if($saved === true)
                <span class="ms-3 fw-bold text-success d-block">SAVED!</span>
            @endif
        </div>
    </form>
</div>
