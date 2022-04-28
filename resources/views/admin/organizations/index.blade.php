@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">Organizations</div>

                    <div class="card-body">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Founded</th>
                                <th>Website</th>
                                <th>Summary</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($organizations as $organization)
                                <tr>
                                    <td>{{ $organization->name }}</td>
                                    <td>{{ $organization->type }}</td>
                                    <td>{{ $organization->founded_date }}</td>
                                    <td>{{ $organization->website }}</td>
                                    <td>{{ $organization->summary }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

