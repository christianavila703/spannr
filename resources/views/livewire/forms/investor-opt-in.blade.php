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
                            <label for="email" class="form-label d-block mb-2 fw-bold">Email<span class="required">*</span></label>
                            <input type="email" class="form-control" wire:model="email" id="email">
                            @error('email') <span class="form-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="field-group-phone col-12 col-md-6 @if($formType === 'horizontal') mb-5 @else mb-4 @endif">
                            <label for="phone" class="form-label d-block mb-2 fw-bold">Phone<span class="required">*</span></label>
                            <input type="tel" class="form-control" wire:model="phone" id="phone">
                            @error('phone') <span class="form-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="field-group-accredited col-12 mb-4 @if($formType === 'horizontal') col-md-6 @endif">
                            <label for="accredited_investor" class="form-label d-block mb-0 fw-bold">Are you an accredited investor?<span class="required">*</span></label>
                            <p class="mb-2 small @if($formType === 'vertical') text-muted @endif">
                                An accredited investor is defined as someone who covers any of the following: 1. Individuals with net assets over $1 million, excluding the primary residence. 2. Have an annual income exceeding $200,000 ($300,000 for joint income) for the last two years with the expectation of earning the same or a higher income in the current year. 3. Is a financial professional holding a FINRA Series 7, 62, or 65. 4. An institution with over $5 million in assets.
                            </p>
                            @error('accredited') <span class="form-error">{{ $accredited }}</span> @enderror
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" wire:model="accredited" id="accredited_yes" value="{{ \App\Models\FormEntry::INVESTOR_ACCREDITED_YES }}">
                                    <label class="form-check-label" for="accredited_yes">{{ \App\Models\FormEntry::INVESTOR_ACCREDITED_YES }}</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" wire:model="accredited" id="accredited_no" value="{{ \App\Models\FormEntry::INVESTOR_ACCREDITED_NO }}">
                                    <label class="form-check-label" for="accredited_no">{{ \App\Models\FormEntry::INVESTOR_ACCREDITED_NO }}</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" wire:model="accredited" id="accredited_unsure" value="{{ \App\Models\FormEntry::INVESTOR_ACCREDITED_UNSURE }}">
                                    <label class="form-check-label" for="accredited_unsure">{{ \App\Models\FormEntry::INVESTOR_ACCREDITED_UNSURE }}</label>
                                </div>
                            </div>
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
