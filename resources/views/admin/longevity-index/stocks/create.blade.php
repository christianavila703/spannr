@extends('layouts.app')

@section('content')
    <div class="container">
        @include('admin.includes.status-messages')

        <h1>Add Company to Index</h1>

        <form action="{{ route('admin.longevity-index.stocks.store') }}" method="POST" class="max-width-420">
            @csrf
            <div class="mb-3">
                <select name="organization_id" id="organization_id" class="js-choice-tag" aria-label="Organization" required>
                    <option value="">Select Organization</option>
                </select>
            </div>
            <div class="mb-3">
                <input type="text" name="ticker" placeholder="Ticker Symbol" aria-label="Ticker symbol" class="form-control">
            </div>
            <div class="mb-3 d-flex">
                <label for="date_added" class="fw-bold text-nowrap me-3">Date Added</label>
                <input type="date" class="form-control" name="date_added" id="date_added">
            </div>
            <div class="mb-3">
                <div class="form-check form-check-inline d-flex align-items-center">
                    <input class="form-check-input" type="checkbox" name="status" id="status-{{ $inactiveStatus }}" value="{{ $inactiveStatus }}">
                    <label class="form-check-label fw-bold text-uppercase mt-1 ms-2 text-tertiary" for="status-{{ $inactiveStatus }}">
                        Mark this {{ $inactiveStatus }}
                    </label>
                </div>
            </div>
            <div>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>

    <script>
        const tagChoice = new Choices('.js-choice-tag');

        tagChoice.setChoices(async () => {
            try {
                const items = await fetch('/admin/json/organizations.json');
                return items.json();
            } catch (err) {
                console.error(err);
            }
        });
    </script>
@endsection
