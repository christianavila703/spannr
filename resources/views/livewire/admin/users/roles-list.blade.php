<div class="max-width-800">
    <table class="table">
        <thead>
            <tr>
                <th>Role</th>
                <th>Permissions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($roles as $role)
                <tr>
                    <td class="lead">
                        <span class="badge {{ $role->color }}">{{ $role->name }}</span>
                    </td>
                    <td>
                        @foreach ($role->permissions as $permission)
                            <span class="me-2">{{ $permission->name }}</span>
                            @if(!$loop->last)<span class="text-light-blue me-2">/</span>@endif
                        @endforeach
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
