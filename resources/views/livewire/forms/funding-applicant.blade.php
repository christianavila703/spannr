<div>
    <form wire:submit.prevent="submit" class="{{ $formClass }} @if($formType === 'vertical') max-width-800 @endif mx-auto">
        @csrf
        <div>
            @if ($showSuccess === true)
                <div class="py-5" style="min-height: 500px">
                    <p class="{{ $successClass }}">Thanks for your interest! We will get in touch with you shortly.</p>
                </div>
            @else
                <div>
                    <div class="row">
                        <div class="field-group-name col-12 @if($formType === 'horizontal') col-md-6 @endif @if($formType === 'horizontal') mb-5 @else mb-4 @endif">
                            <label for="first_name" class="form-label d-block mb-2 fw-bold">Name<span class="required">*</span></label>
                            <div class="row">
                                <div class="col-12 col-md-6 mb-3">
                                    <input type="text" class="form-control" wire:model="first_name" id="first_name" placeholder="First">
                                    @error('first_name') <span class="form-error">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <input type="text" class="form-control" wire:model="last_name" id="last_name" placeholder="Last">
                                    @error('last_name') <span class="form-error">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="field-group-email col-12 col-md-6 @if($formType === 'horizontal') mb-5 @else mb-4 @endif">
                            <label for="company_name" class="form-label d-block mb-2 fw-bold">Company name<span class="required">*</span></label>
                            <input type="text" class="form-control" wire:model="company_name" id="company_name">
                            @error('company_name') <span class="form-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="field-group-email col-12 col-md-6 @if($formType === 'horizontal') mb-5 @else mb-4 @endif">
                            <label for="email" class="form-label d-block mb-2 fw-bold">Email<span class="required">*</span></label>
                            <input type="email" class="form-control" wire:model="email" id="email">
                            @error('email') <span class="form-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="field-group-phone col-12 col-md-6 @if($formType === 'horizontal') mb-5 @else mb-4 @endif">
                            <label for="phone" class="form-label d-block mb-2 fw-bold">Phone<span class="required">*</span></label>
                            <input type="tel" class="form-control" wire:model="phone" id="phone">
                            @error('phone') <span class="form-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn @if($formType === 'horizontal' OR $formType === 'ventures') btn-rounded btn-outline @else btn-quintary @endif">Submit</button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </form>
</div>
