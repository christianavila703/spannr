@extends('layouts.app')

@section('content')
    <x-entities.create backUrl="{{ route('admin.organizations.index') }}" backLabel="Organizations">
        <x-slot:title>Add Stock to {{ isset($organization) ? $organization->name : 'Organization' }}</x-slot:title>

        <form action="{{ route('admin.organizations.stock.store') }}" method="POST" class="max-width-420">
            @csrf

            <div class="mb-3">
                @if(isset($organization))
                    <input type="hidden" name="organization_id" value="{{ $organization->id }}">
                @else
                <select name="organization_id" id="organization_id" class="js-choice-tag" aria-label="Organization" required>
                    <option value="">Select Organization</option>
                    @foreach($organizations as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
                @endif
            </div>
            <div class="mb-3">
                <input type="text" name="ticker" placeholder="Ticker Symbol" aria-label="Ticker symbol" class="form-control">
            </div>
            <div class="mb-3 d-flex">
                <label for="date_added" class="fw-bold text-nowrap me-3">Date Added</label>
                <input type="date" class="form-control" name="date_added" id="date_added">
            </div>
            <div class="mb-3">
                <label for="status" class="fw-bold text-nowrap me-3">Status</label>
                <div class="d-flex flex-wrap">
                    @foreach (\App\Models\Stock::STATUSES as $status)
                        <div class="form-check form-check-inline d-flex align-items-center">
                            <input class="form-check-input" type="radio" name="status" id="status-{{ $status }}" value="{{ $status }}" @if ($status === \App\Models\Stock::STATUS_ACTIVE) checked @endif>
                            <label class="form-check-label text-uppercase mt-1 ms-2" for="status-{{ $status }}">
                                {{ $status }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="mb-3">
                <label for="indexed" class="fw-bold text-nowrap me-3">Participate in index?</label>
                <div class="d-flex flex-wrap">
                    @foreach ([0 => 'No', 1 => 'Yes'] as $value => $label)
                        <div class="form-check form-check-inline d-flex align-items-center">
                            <input class="form-check-input" type="radio" name="indexed" id="indexed-{{ $value }}" value="{{ $value }}" @if ($value === 0) checked @endif>
                            <label class="form-check-label text-uppercase mt-1 ms-2" for="indexed-{{ $value }}">
                                {{ $label }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
            <div>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>

    </x-entities.create>

    <script>
        const tagChoice = new Choices('.js-choice-tag');
    </script>
@endsection
