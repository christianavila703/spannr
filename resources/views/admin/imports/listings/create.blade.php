@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="text-primary">Import Listings</h1>

        @include('admin.includes.status-messages')

        <form action="{{ route('admin.import.listings.process') }}" method="post" enctype="multipart/form-data" class="my-3">
            @csrf
            <div class="d-flex max-width-450">
                <div>
                    <input type="file" id="file" name="file" class="form-control form-control-file" required aria-label="File">
                    @error('file') <div class="text-danger">{{ $message }}</div> @enderror
                </div>
                <div class="ms-3">
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </div>
        </form>

        <div class="mt-5 d-md-flex">
            <div class="me-md-5">
                <strong class="fs-5 text-uppercase text-body-emphasis">Required fields:</strong>
                <table class="table w-auto border">
                    <thead>
                        <tr>
                            <th>field name</th>
                            <th>field type</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requiredFields as $field => $type)
                            <tr>
                                <th class="fw-normal">
                                    <code>{{ $field }}</code>
                                </th>
                                <td class="text-body-secondary">
                                    {{ $type }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div>
                <strong class="fs-5 text-uppercase text-body-emphasis">Optional fields:</strong>
                <table class="table table-sm w-auto border">
                    <thead>
                        <tr>
                            <th>field name</th>
                            <th>field type</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($optionalFields as $field => $type)
                            <tr>
                                <th class="fw-normal">
                                    <code>{{ $field }}</code>
                                </th>
                                <td class="text-body-secondary">
                                    {{ $type }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
