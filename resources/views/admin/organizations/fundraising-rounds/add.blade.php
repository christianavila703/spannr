<div class="modal fade" id="addFundraisingRoundForm" tabindex="-1" aria-labelledby="addFundraisingRoundFormLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('fundraising-rounds.store') }}">
                @csrf
                <input type="hidden" name="organization_id" value="{{ $organization->id }}">
                <div class="modal-header">
                    <h5 class="modal-title" id="addFundraisingRoundFormLabel">Add Fundraising Round for {{ $organization->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="min-height: 400px">
                    <div class="row">
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="form-floating">
                                <label for="round" class="visually-hidden">Select Round</label>
                                <select name="round" id="round" class="js-choice-round" required>
                                    <option value="">Select Round</option>
                                    @foreach (\App\Models\FundraisingRound::ROUNDS as $round)
                                        <option value="{{ $round }}">{{ $round }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6 mb-3">
                            <label for="investors" class="visually-hidden">Select Investors</label>
                            <select multiple name="investors[]" id="investors" class="js-choice-fund-round-investor">
                                <option value="">Select Investors</option>
                                @foreach ($investors as $investor)
                                    <option value="{{ $investor->id }}">{{ $investor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-lg-6 mb-3">
                            <x-forms.input type="date" name="date" label="Date" required="true" value="{{ $fundraisingRound->date ?? old('date') }}" />
                        </div>
                        <div class="col-12 col-lg-6 mb-3">
                            <x-forms.input type="url" name="announcement_url" label="Announcement URL" value="{{ $fundraisingRound->announcement_url ?? old('announcement_url') }}" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-lg-6 mb-3">
                            <x-forms.input type="number" name="amount_raised" label="Amount Raised" value="{{ $fundraisingRound->amount_raised ?? old('amount_raised') }}" />
                        </div>
                        <div class="col-12 col-lg-6 mb-3">
                            <x-forms.input type="number" name="valuation" label="Valuation" value="{{ $fundraisingRound->valuation ?? old('valuation') }}" />
                        </div>
                    </div>
                    <div class="form-floating mb-3">
                        <textarea class="form-control floating-textarea-sm" id="description" name="description" placeholder="Description">{{ $fundraisingRound->description ?? old('description') }}</textarea>
                        <label for="description">Description</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const fundRoundChoice = new Choices('.js-choice-round');
    const fundRoundInvestorChoice = new Choices('.js-choice-fund-round-investor');
</script>
