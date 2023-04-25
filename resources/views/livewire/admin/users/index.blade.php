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
                         @else
                             <button class="btn btn-tertiary" type="button" data-bs-toggle="modal"
                                     data-bs-target="#assignPersonForm" data-user-id="{{ $user->id }}">Assign Person
                             </button>
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

<div class="modal fade" id="assignPersonForm" tabindex="-1" aria-labelledby="assignPersonFormLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.users.assign-person') }}">
                @csrf
                <input type="hidden" id="assignUserId" name="user_id" value="" />

                <div class="modal-header">
                    <h5 class="modal-title">Assign Person</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="min-height: 400px;">
                    <label for="assign_person_people_select">Select Person</label>
                    <select name="person_id" id="assignPersonId" required>
                        <option value=""></option>
                        @foreach($unassignedPeople as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    new Choices('#assignPersonId');

    let assignPersonFormModal = document.getElementById('assignPersonForm');
    let assignUserIdInput = document.getElementById('assignUserId');

    assignPersonFormModal.addEventListener('shown.bs.modal', function (event) {
        assignUserIdInput.value = event.relatedTarget.dataset.userId;
    });
</script>
