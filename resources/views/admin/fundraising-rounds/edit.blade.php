@extends('layouts.app')

@section('content')

    <x-entities.create backUrl="{{ route('admin.fundraising-rounds.show', $fundraisingRound->id) }}" backLabel="{{ $fundraisingRound->name }}">
        <x-slot:title>Edit Fundraising Round</x-slot:title>
        <form method="POST" action="{{ route('admin.fundraising-rounds.update', $fundraisingRound->id) }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="organization_id" value="{{ $fundraisingRound->organization_id }}">
            <div class="row">
                <div class="col-12 col-lg-6 mb-3">
                    <div class="form-floating">
                        <label for="round" class="visually-hidden">Select Round</label>
                        <select name="round" id="round" class="js-choice-round" required>
                            <option value="">Select Round</option>
                            @foreach (\App\Models\FundraisingRound::ROUNDS as $round)
                                <option value="{{ $round }}" @if($fundraisingRound->round === $round) selected @endif>{{ $round }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-12 col-lg-6 mb-3">
                    <label for="investors" class="visually-hidden">Select Investors</label>
                    <select multiple name="investors[]" id="investors" class="js-choice-fund-round-investor">
                        <option value="">Select Investors</option>
                        @foreach ($allInvestors as $investor)
                            <option value="{{ $investor->id }}" @if (in_array($investor->id, $currentInvestors)) selected @endif>{{ $investor->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-lg-6 mb-3">
                    <x-forms.input type="date" name="date" label="Date" required="true" value="{{ old('date') ?? $fundraisingRound->date }}" />
                </div>
                <div class="col-12 col-lg-6 mb-3">
                    <x-forms.input type="url" name="announcement_url" label="Announcement URL" value="{{ old('announcement_url') ?? $fundraisingRound->announcement_url }}" />
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-lg-6 mb-3">
                    <x-forms.input type="number" name="amount_raised" label="Amount Raised" value="{{ old('amount_raised') ?? $fundraisingRound->amount_raised }}" />
                </div>
                <div class="col-12 col-lg-6 mb-3">
                    <x-forms.input type="number" name="valuation" label="Valuation" value="{{ old('valuation') ?? $fundraisingRound->valuation }}" />
                </div>
            </div>
            <div class="form-floating mb-3">
                <textarea class="form-control floating-textarea-sm" id="description" name="description" placeholder="Description">{{ old('description') ?? $fundraisingRound->description }}</textarea>
                <label for="description">Description</label>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </form>
    </x-entities.create>

@endsection

@push('end')
    <script>
        const fundRoundChoice = new Choices('.js-choice-round');
        const fundRoundInvestorChoice = new Choices('.js-choice-fund-round-investor');
    </script>
@endpush
