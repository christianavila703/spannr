<div>
    <form wire:submit.prevent="submit" class="max-width-600 mx-auto">
        @csrf
        <div>
            @if ($showSuccess === true)
                <div class="py-5" style="min-height: 500px">
                    <p class="{{ $successClass }}">Thanks for your message! We will get in touch with you shortly.</p>
                </div>
            @else
                <div class="row">
                    <div class="col-12 mb-2">
                        <label for="first_name" class="form-label d-block mb-2 fw-bold">Name<span class="required">*</span></label>
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
                    <div class="col-12 mb-4">
                        <label for="email" class="form-label d-block mb-2 fw-bold">Email<span class="required">*</span></label>
                        <input type="email" class="form-control" wire:model="email" id="email">
                        @error('email') <span class="form-error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-12 mb-4">
                        <label for="subject" class="form-label d-block mb-2 fw-bold">What is this regarding?<span class="required">*</span></label>
                        <select wire:model="subject" name="subject" id="subject" class="form-select">
                            <option selected disabled value=""></option>
                            @foreach($reasons as $reason)
                                <option value="{{ $reason }}">{{ $reason }}</option>
                            @endforeach
                        </select>
                        @error('subject') <span class="form-error text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-12 mb-4">
                        <label for="message" class="form-label d-block mb-0 fw-bold">Message<span class="required">*</span></label>
                        @error('message') <span class="form-error text-danger">{{ $message }}</span> @enderror
                        <textarea class="form-control mt-2" wire:model="message" id="message" rows="8"></textarea>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-quintary">Submit</button>
                    </div>
                </div>
            @endif
        </div>
    </form>
</div>
