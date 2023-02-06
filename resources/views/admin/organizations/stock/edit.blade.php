@extends('layouts.app')

@section('content')
    <x-entities.create backUrl="{{ route('admin.organizations.show', $stock->organization->slug) }}" backLabel="{{ $stock->organization->name }}">
        <x-slot:title>Update {{ $stock->name }} stock</x-slot:title>

        <form action="{{ route('admin.organizations.stock.update', $stock->id) }}" method="POST" class="max-width-420">
            @csrf
            @method('put')
            <div class="mb-3">
                <label for="date_added" class="fw-bold">Ticker</label>
                <input type="text" name="ticker" value="{{ $stock->ticker }}" aria-label="Ticker symbol" class="form-control">
            </div>
            <div class="mb-3">
                <label for="date_added" class="fw-bold">Date Added</label>
                <input type="date" class="form-control" name="date_added" id="date_added" value="{{ $stock->date_added }}">
            </div>
            <div class="mb-3">
                <label for="date_removed" class="fw-bold">Date Removed</label>
                <input type="date" class="form-control" name="date_removed" id="date_removed" value="{{ $stock->date_removed }}">
            </div>
            <div class="mb-3">
                <label for="status" class="fw-bold text-nowrap me-3">Status</label>
                <div class="d-flex flex-wrap">
                    @foreach (\App\Models\Stock::STATUSES as $status)
                        <div class="form-check form-check-inline d-flex align-items-center">
                            <input class="form-check-input" type="radio" name="status" id="status-{{ $status }}" value="{{ $status }}" @if ($stock->status === $status) checked @endif>
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
                            <input class="form-check-input" type="radio" name="indexed" id="indexed-{{ $value }}" value="{{ $value }}" @if ($stock->indexed === $value) checked @endif>
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
        <form class="mt-4" action="{{ route('admin.organizations.stock.destroy', $stock->id) }}" method="post">
            @csrf
            @method('delete')
            <button type="submit" class="btn btn-danger confirm-button">Delete stock</button>
        </form>
    </x-entities.create>

@endsection
