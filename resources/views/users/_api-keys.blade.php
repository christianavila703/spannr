<div>
    <h3>API Keys</h3>
    @if (session('apiKeyResponse'))
        <div class="alert alert-success alert-dismissible fade show">
            <div>
                {{ session('apiKeyResponse') }}
                @if (session('apiToken'))
                    <span class="d-block fw-bold lead">{{ session('apiToken') }}</span>
                @endif
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <form action="{{ route('users.api.create') }}" method="POST" class="max-width-600">
        @csrf
        <div class="d-flex">
            <label for="token_name" class="visually-hidden">Token Name</label>
            <input type="text" class="form-control me-2" name="token_name" id="token_name" placeholder="Key Name / Description">
            <button class="btn btn-quaternary text-nowrap confirm-button">Create API Key</button>
        </div>
    </form>
    @if ($apiKeys->count() > 0)
        <table class="table mt-4 align-middle table-no-bottom-border mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Created</th>
                    <th>Name / Description</th>
                    <th>Scope</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($apiKeys as $apiKey)
                    <tr>
                        <td>
                            {{ \Carbon\Carbon::parse($apiKey->created_at)->format('M d, Y') }}
                        </td>
                        <td>{{ $apiKey->name }}</td>
                        <td>
                            @foreach ($apiKey->abilities as $ability)
                                {{ $ability }}
                            @endforeach
                        </td>
                        <td class="text-end">
                            <form action="{{ route('users.api.delete') }}" method="POST" class="max-width-600">
                                @csrf
                                <input type="hidden" name="token_id" value="{{ $apiKey->id }}">
                                <button type="submit" class="btn btn-sm btn-danger confirm-button"><i class="fa-solid fa-trash-can me-1"></i>delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
