<div class="card card-body">
    <h2 class="h3">User Settings</h2>

    <div class="fs-5">
            <table class="table w-auto table-no-bottom-border mb-0">
                <tr>
                    <th>Name:</th>
                    <td>{{ $user->full_name }}</td>
                </tr>
                <tr>
                    <th>Email:</th>
                    <td>{{ $user->email }}</td>
                </tr>
            </table>
        </div>

    <div class="mt-3">
        <a href="{{ route('users.settings') }}" class="btn btn-primary">Change Password</a>
    </div>
</div>
