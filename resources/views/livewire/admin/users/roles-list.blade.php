<div>
    <table class="table align-middle">
        <thead class="table-dark">
            <tr>
                <th>Role</th>
                <th>Users</th>
                <th>Permissions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($roles as $role)
                <tr wire:key="role-{{ $role->id }}">
                    <td class="lead">
                        <span class="badge {{ $role->color }}">{{ $role->name }}</span>
                    </td>
                    <td>{{ $role->users_count }}</td>
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
