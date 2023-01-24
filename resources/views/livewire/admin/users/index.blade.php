<div>
     <table class="table">
         <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Byline</th>
                <th>Role</th>
                <th>Created</th>
                <th>Person</th>
                <th></th>
            </tr>
         </thead>
         <tbody>
             @foreach ($users as $user)
                 <tr class="no-bottom-border">
                     <td>
                         <span class="lead fw-bold">{{ $user->full_name }}</span>
                     </td>
                     <td>{{ $user->email }}</td>
                     <td>{{ $user->byline }}</td>
                     <td>
                         @foreach ($user->roles as $role)
                             <span class="badge {{ $role->color }}">{{ $role->name }}</span>
                         @endforeach
                     </td>
                     <td>{{ $user->created_date }}</td>
                     <td>
                         @if ($user->person)
                             <a href="{{ route('admin.people.show', $user->person->slug) }}">{{ $user->person->name }}</a>
                         @endif
                     </td>
                     <td>
                         <button class="btn btn-secondary" type="button"
                                 data-bs-toggle="collapse" data-bs-target="#collapse{{ $user->id }}" aria-expanded="false" aria-controls="collapse{{ $user->id }}">
                             Update Profile
                        </button>
                     </td>
                 </tr>
                 <tr>
                     <td colspan="7">
                         <div class="collapse pb-4" id="collapse{{ $user->id }}">
                             <div>
                                 <livewire:users.user-profile :user="$user" />
                             </div>
                        </div>
                     </td>
                 </tr>
             @endforeach
         </tbody>
     </table>

    <div class="my-4">
        {{ $users->links() }}
    </div>

    <style>
        .no-bottom-border td {
            border-bottom: 0;
            padding-top: 1.25rem;
        }
    </style>
</div>
