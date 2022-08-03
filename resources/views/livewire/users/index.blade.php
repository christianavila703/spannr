<div class="max-width-800">
     <table class="table">
         <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Created</th>
            </tr>
         </thead>
         <tbody>
             @foreach ($users as $user)
                 <tr>
                     <td>{{ $user->name }}</td>
                     <td>{{ $user->email }}</td>
                     <td>
                         @foreach ($user->roles as $role)
                             <span class="badge {{ $role->color }}">{{ $role->name }}</span>
                         @endforeach
                     </td>
                     <td>{{ $user->created_date }}</td>
                 </tr>
             @endforeach
         </tbody>
     </table>

    <div class="my-4">
        {{ $users->links() }}
    </div>
</div>
