<div>
    <form wire:submit.prevent="submit" class="max-width-600 mx-auto py-lg-4">
        @csrf
        <div>
            @if ($showSuccess === true)
                <div class="py-5">
                    <p class="{{ $successClass }}">Your message has been sent!</p>
                </div>
            @else
                <div class="row">
                    <div class="col-12 mb-2">
                        <label for="first_name" class="form-label d-block mb-2 fw-bold">Your Name<span class="required">*</span></label>
                        <div class="row">
                            <div class="col-12 col-md-6 mb-3">
                                <input type="text" class="form-control" wire:model="first_name" id="first_name" placeholder="First">
                                @error('first_name') <span class="form-error text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <input type="text" class="form-control" wire:model="last_name" id="last_name" placeholder="Last">
                                @error('last_name') <span class="form-error text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 mb-4">
                        <label for="email" class="form-label d-block mb-2 fw-bold">Email<span class="required">*</span></label>
                        <input type="email" class="form-control" wire:model="email" id="email">
                        @error('email') <span class="form-error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-12 col-md-6 mb-4">
                        <label for="phone" class="form-label d-block mb-2 fw-bold">Phone</label>
                        <input type="text" class="form-control" wire:model="phone" id="phone" placeholder="(555) 555-5555">
                        @error('phone') <span class="form-error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-12 mb-4">
                        <label for="message" class="form-label d-block mb-0 fw-bold">Message<span class="required">*</span></label>
                        @error('message') <span class="form-error text-danger">{{ $message }}</span> @enderror
                        <textarea class="form-control mt-2" wire:model="message" id="message" rows="8"></textarea>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-quintary">Send Email</button>
                    </div>
                </div>
            @endif
        </div>
    </form>
</div>
